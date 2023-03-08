<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\MessageHandler;

use App\Entity\Account;
use App\Entity\BinanceAccount;
use App\Message\AllTransactionsMessage;
use App\Message\AllTransfersMessage;
use App\Message\NewTransactions;
use App\Message\OwnedTransactionsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class TransactionHandler
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[AsMessageHandler]
    public function handleFetchTransactionsOfSymbolsOwned(OwnedTransactionsMessage $message): void
    {
        $accountId      = $message->getAccountId();
        /** @var Account $account */
        $account        = $this->manager->getRepository(Account::class)->find($accountId);
        if (null === $account) {
            throw new \Exception('Account not found');
        }
        $accountRepository = $this->manager->getRepository(BinanceAccount::class);
        if (!method_exists($accountRepository, 'getAccountSymbols')) {
            throw new \Exception('Method getAccountSymbols not found');
        }
        $accountSymbols = $accountRepository->getAccountSymbols($account);
        // Fetch and save transactions for owned symbols
        $error = null;
        try {
            $this->fetchTransactions($accountSymbols, $account);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        $account->setSynchroStep(step: Account::STEP_TRANSACTION_SYMBOL_OWNED, startedAt: new \DateTime(), endedAt: new \DateTime(), error: $error);
        $this->manager->flush();
    }

    #[AsMessageHandler]
    public function handleFetchTransactionsOfSymbolsNotOwned(AllTransactionsMessage $message): void
    {
        $accountId         = $message->getAccountId();
        /** @var Account $account */
        $account           = $this->manager->getRepository(Account::class)->find($accountId);
        if (null === $account) {
            throw new \Exception('Account not found');
        }
        $accountRepository = $this->manager->getRepository(BinanceAccount::class);
        if (!method_exists($accountRepository, 'getAccountSymbols') || !method_exists($accountRepository, 'getAllSymbols') || !method_exists($accountRepository, 'fetchTransfers')) {
            throw new \Exception('Method getAccountSymbols not found');
        }
        $accountSymbols = $accountRepository->getAccountSymbols($account); // user account symbols
        $allSymbols     = $accountRepository->getAllSymbols($account); // all symbols

        // Remove symbols owned by user
        $symbolsNotOwned = array_diff($allSymbols, $accountSymbols); // symbols not owned by user

        // Fetch and save transactions for owned symbols
        $error = null;
        try {
            $this->fetchTransactions($symbolsNotOwned, $account);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        $account->setSynchroStep(step: Account::STEP_TRANSACTION_SYMBOL_NOT_OWNED, startedAt: new \DateTime(), endedAt: new \DateTime(), error: $error);
        $this->manager->flush();
    }

    #[AsMessageHandler]
    public function handleFetchTransfers(AllTransfersMessage $message): void
    {
        $startDate = new \DateTime();
        $accountId = $message->getAccountId();
        /** @var Account $account */
        $account   = $this->manager->getRepository(Account::class)->find($accountId);
        if (null === $account) {
            throw new \Exception('Account not found');
        }
        // Fetch and save Transfers
        $accountRepository = $this->manager->getRepository(BinanceAccount::class);
        if (!method_exists($accountRepository, 'fetchTransfers')) {
            throw new \Exception('Method fetchTransfers not found');
        }
        $error = null;
        try {
            $accountRepository->fetchTransfers($account);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        $account->setSynchroStep(step: Account::STEP_FETCH_TRANSFERS, startedAt: $startDate, endedAt: new \DateTime(), error: $error);
        $this->manager->flush();
    }

    #[AsMessageHandler]
    public function handleNewTransaction(NewTransactions $message): void
    {
        $accountId      = $message->getAccountId();
        $account        = $this->manager->getRepository(Account::class)->find($accountId);
        if (null === $account) {
            throw new \Exception('Account not found');
        }
        $accountRepository = $this->manager->getRepository(BinanceAccount::class);
        if (!method_exists($accountRepository, 'getAccountSymbols')) {
            throw new \Exception('Method getAccountSymbols not found');
        }
        $allSymbols     = $accountRepository->getAllSymbols($account); // all symbols

        // Fetch and save transactions for owned symbols
        $this->fetchTransactions($allSymbols, $account, true);
    }

    private function fetchTransactions(array $accountSymbols, Account $account, ?bool $new = false): void
    {
        $accountRepository = $this->manager->getRepository(BinanceAccount::class);
        if (!method_exists($accountRepository, 'fetchTransactions')) {
            throw new \Exception('Method fetchTransactions not found');
        }
        // Fetch transactions for owned symbols

        if ($new) {
            $transactions = $accountRepository->fetchTransactions($account, $accountSymbols, true);
        } else {
            $transactions = $accountRepository->fetchTransactions($account, $accountSymbols);
        }

        // Save transactions into database
        $accountRepository->registerTransaction($transactions, $account, $this->manager);
    }
}
