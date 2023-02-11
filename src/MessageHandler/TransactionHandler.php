<?php

declare(strict_types=1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\MessageHandler;

use App\Entity\Account;
use App\Entity\BinanceAccount;
use App\Entity\Currency;
use App\Entity\Holding;
use App\Entity\Symbol;
use App\Entity\Transaction;
use App\Message\AllTransactionsMessage;
use App\Message\AllTransfertsMessage;
use App\Message\OwnedTransactionsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class TransactionHandler
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    /**
     * @param OwnedTransactionsMessage $message
     * @return void
     */
    #[AsMessageHandler]
    public function handleFetchTransactionsOfSymbolsOwned(OwnedTransactionsMessage $message): void
    {
        $accountId = $message->getAccountId();
        $account = $this->manager->getRepository(Account::class)->find($accountId);
        $accountSymbols = $this->manager->getRepository($account::class)->getAccountSymbols($account);
        // Fetch and save transactions for owned symbols
        $this->fetchTransactions($accountSymbols, $account);

        // Update holdings
        //$this->manager->getRepository(Holding::class)->updateHoldings($account->getUser());
    }

    /**
     * @param AllTransactionsMessage $message
     * @return void
     */
    #[AsMessageHandler]
    public function handleFetchTransactionsOfSymbolsNotOwned(AllTransactionsMessage $message): void
    {
        $accountId = $message->getAccountId();
        $account = $this->manager->getRepository(Account::class)->find($accountId);
        $accountRepository = $this->manager->getRepository($account::class);
        $holdingRepository = $this->manager->getRepository(Holding::class);

        $accountSymbols = $accountRepository->getAccountSymbols($account); // user account symbols
        $allSymbols = $accountRepository->getAllSymbols($account); // all symbols

        // Remove symbols owned by user
        $symbolsNotOwned = array_diff($allSymbols, $accountSymbols); // symbols not owned by user

        // Fetch and save transactions for owned symbols
        $this->fetchTransactions($symbolsNotOwned, $account);

        // Fetch and save Transferts
        $this->manager->getRepository($account::class)->fetchTransferts($account);

        // Update holdings
        //$holdingRepository->updateHoldings($account->getUser());
    }


    /**
     * @param AllTransfertsMessage $message
     * @return void
     */
    #[AsMessageHandler]
    public function handleFetchTransferts(AllTransfertsMessage $message): void
    {
        $accountId = $message->getAccountId();
        $account = $this->manager->getRepository(Account::class)->find($accountId);

        // Fetch and save Transferts
        $this->manager->getRepository(BinanceAccount::class)->fetchTransferts($account);
    }


    private function fetchTransactions(array $accountSymbols, Account $account): void
    {
        // Fetch transactions for owned symbols
        $transactions = $this->manager->getRepository($account::class)->fetchTransactions($account, $accountSymbols);

        // Save transactions into database
        $this->saveTransactions($transactions, $account, $this->manager);
    }

    private function saveTransactions(array $transactions, Account $account, EntityManagerInterface $manager): void
    {
        // Register transactions
        foreach ($transactions as $transaction) {
            // Code ISO
            $codeIso = $transaction['symbol'];

            //If symbol is not in the database, create it
            $symbol = $manager->getRepository(Symbol::class)->findOneBy(['id' => $codeIso]);
            if (null === $symbol) {
                $newSymbol = new Symbol();
                $newSymbol->setId($codeIso);
                $symbol = $newSymbol;
                $manager->persist($symbol);
                $manager->flush();
            }

            // Date
            $date = new \DateTime();
            $date->setTimestamp($transaction['time']);

            // Order Direction
            if ($transaction['isBuyer']) {
                $orderDirection = 'BUY';
            } else {
                $orderDirection = 'SELL';
            }

            // Transaction ID
            // $transactionExists = $this->manager->getRepository(Transaction::class)->findOneBy(['transactionExchangeId' => $exchangeTradeId]);

            $transactionPrice = (float)$transaction['price'];
            $transactionQuantity = (float)$transaction['qty'];
            $externalTransactionId = (string)$transaction['id'];

            // if (null === $transactionExists && $binanceExchange instanceof Exchange) {
            $newTransaction = new Transaction();
            $newTransaction->setAccount($account)
                ->setSymbol($symbol)
                ->setDate($date)
                ->setOrderDirection($orderDirection)
                ->setPrice($transactionPrice)
                ->setQuantity($transactionQuantity)
                ->setExternalTransactionId($externalTransactionId);

            $manager->persist($newTransaction);
            $manager->flush();
            // }
        }
    }
}
