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
use App\Entity\Currency;
use App\Entity\Holding;
use App\Entity\Transaction;
use App\Entity\User;
use App\Message\BinanceAllTransactionsMessage;
use App\Message\BinanceOwnedTransactionsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class BinanceHandler
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[AsMessageHandler]
    public function handleFetchBinanceTransactionsOfSymbolsOwned(BinanceOwnedTransactionsMessage $fetchBinanceTransactions): void
    {
        $account = $this->manager->getRepository(Account::class)->find($fetchBinanceTransactions->getAccountId());

        if (!$account instanceof Account) {
            return;
        }

        $accountSymbols = $this->manager->getRepository(BinanceAccount::class)->getAccountSymbols($account); // user account symbols

        // Fetch and save transactions for owned symbols
        $this->fetchTransactions($accountSymbols, $account);

        // Update holdings
        $this->manager->getRepository(Holding::class)->updateHoldings($account->getUser());
    }

    #[AsMessageHandler]
    public function handleFetchBinanceTransactionsOfSymbolsNotOwned(BinanceAllTransactionsMessage $fetchBinanceTransactions): void
    {
        $account = $this->manager->getRepository(Account::class)->find($fetchBinanceTransactions->getAccountId());

        if (!$account instanceof Account) {
            return;
        }

        $accountSymbols = $this->manager->getRepository(BinanceAccount::class)->getAccountSymbols($account); // user account symbols
        $allSymbols     = $this->manager->getRepository(BinanceAccount::class)->getAllSymbols($account); // all symbols

        // Remove symbols owned by user
        $symbolsNotOwned = array_diff($allSymbols, $accountSymbols); // symbols not owned by user

        // Fetch and save transactions for owned symbols
        $this->fetchTransactions($symbolsNotOwned, $account);
    }

    private function fetchTransactions(array $accountSymbols, Account $account): void
    {
        // Fetch transactions for owned symbols
        $transactions = $this->manager->getRepository(BinanceAccount::class)->fetchTransactions($account, $accountSymbols);

        // Save transactions into database
        $this->saveTransactions($transactions, $account, $this->manager);
    }

    private function saveTransactions(array $transactions, Account $account, EntityManagerInterface $manager): void
    {
        // Register transactions
        foreach ($transactions as $transaction) {
            // Code ISO
            $codeIso = $transaction['symbol'];

            // Currency ID
            $currencyId = $manager->getRepository(Currency::class)->findOneBy(['codeIso' => $codeIso]);
            if (null === $currencyId) {
                $newCurrency = new Currency();
                $newCurrency->setCodeIso($codeIso)->setName($codeIso);
                $currencyId = $newCurrency;
                $manager->persist($currencyId);
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

            $transactionPrice      = (float) $transaction['price'];
            $transactionQuantity   = (float) $transaction['qty'];
            $externalTransactionId = (string) $transaction['id'];

            // if (null === $transactionExists && $binanceExchange instanceof Exchange) {
            $newTransaction = new Transaction();
            $newTransaction->setAccount($account)
                ->setUser($account->getUser())
                ->setCurrency($currencyId)
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
