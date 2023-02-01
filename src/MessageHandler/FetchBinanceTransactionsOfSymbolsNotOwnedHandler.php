<?php

namespace App\MessageHandler;

use App\Entity\Account;
use App\Entity\BinanceAccount;
use App\Entity\Currency;
use App\Entity\Exchange;
use App\Entity\Holding;
use App\Entity\Transaction;
use App\Entity\User;
use App\Message\FetchBinanceTransactions;
use App\Message\FetchBinanceTransactionsOfSymbolsNotOwned;
use Doctrine\ORM\EntityManagerInterface;

class FetchBinanceTransactionsOfSymbolsNotOwnedHandler
{
    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    public function handleFetchBinanceTransactionsOfSymbolsNotOwned(FetchBinanceTransactionsOfSymbolsNotOwned $fetchBinanceTransactions): void
    {
        $account = $this->manager->getRepository(Account::class)->find($fetchBinanceTransactions->getAccountId());
        $accountSymbols = $this->manager->getRepository(BinanceAccount::class)->getAccountSymbols($account); //user account symbols
        $allSymbols = $this->manager->getRepository(BinanceAccount::class)->getAllSymbols(); //all symbols

        // Remove symbols owned by user
        $symbolsNotOwned = array_diff($allSymbols, $accountSymbols); //symbols not owned by user

        if (!$account instanceof Account) {
            return;
        }

        // Fetch transactions for not owned symbols
        $transactions = $this->manager->getRepository(BinanceAccount::class)->fetchTransactions($account, $symbolsNotOwned, $fetchBinanceTransactions->getPreviousUpdate());

        // Binance ID
        $binanceExchange = $this->manager->getRepository(Exchange::class)->findOneBy(['label' => 'Binance']);

        //Save transactions into database
        $this->saveTransactions($transactions, $account, $binanceExchange, $this->manager);

    }


    /**
     * @param array $transactions
     * @param Account $account
     * @param Exchange $binanceExchange
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function saveTransactions(array $transactions, Account $account, Exchange $binanceExchange, EntityManagerInterface $manager): void
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
            $exchangeTradeId = $transaction['id'];
            //$transactionExists = $this->manager->getRepository(Transaction::class)->findOneBy(['transactionExchangeId' => $exchangeTradeId]);
            $transactionExists = null;

            $transactionPrice = (float)$transaction['price'];
            $transactionQuantity = (float)$transaction['qty'];

            //if (null === $transactionExists && $binanceExchange instanceof Exchange) {
            if (true) {
                $newTransaction = new Transaction();
                $newTransaction->setExchange($binanceExchange)
                    ->setUser($account->getUser())
                    ->setCurrency($currencyId)
                    ->setDate($date)
                    ->setOrderDirection($orderDirection)
                    ->setPrice($transactionPrice)
                    ->setQuantity($transactionQuantity)//->setTransactionExchangeId(null)
                ;

                $manager->persist($newTransaction);
                $manager->flush();
            }
        }
    }
}