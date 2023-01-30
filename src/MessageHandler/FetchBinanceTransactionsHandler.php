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
use App\Entity\Exchange;
use App\Entity\Holding;
use App\Entity\Transaction;
use App\Entity\User;
use App\Message\FetchBinanceTransactions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class FetchBinanceTransactionsHandler
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    public function __invoke(FetchBinanceTransactions $fetchBinanceTransactions): void
    {
        $account = $this->manager->getRepository(Account::class)->find($fetchBinanceTransactions->getAccountId());
        $accountSymbols = $this->manager->getRepository(BinanceAccount::class)->getAccountSymbols($account); //user account symbols


        if (!$account instanceof Account) {
            return;
        }

        $transactions = $this->manager->getRepository(BinanceAccount::class)->fetchTransactions($account, $accountSymbols, $fetchBinanceTransactions->getPreviousUpdate());

        // Binance ID
        $binanceExchange = $this->manager->getRepository(Exchange::class)->findOneBy(['label' => 'Binance']);

        // Register transactions
        foreach ($transactions as $transaction) {
            // Code ISO
            $codeIso = $transaction['symbol'];

            // Currency ID
            $currencyId = $this->manager->getRepository(Currency::class)->findOneBy(['codeIso' => $codeIso]);
            if (null === $currencyId) {
                $newCurrency = new Currency();
                $newCurrency->setCodeIso($codeIso)->setName($codeIso);
                $currencyId = $newCurrency;
                $this->manager->persist($currencyId);
                $this->manager->flush();
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

            $transactionPrice = (float) $transaction['price'];
            $transactionQuantity = (float) $transaction['qty'];

            //if (null === $transactionExists && $binanceExchange instanceof Exchange) {
            if (true) {
                $newTransaction = new Transaction();
                $newTransaction->setExchange($binanceExchange)
                    ->setUser($account->getUser())
                    ->setCurrency($currencyId)
                    ->setDate($date)
                    ->setOrderDirection($orderDirection)
                    ->setPrice($transactionPrice)
                    ->setQuantity($transactionQuantity)
                    //->setTransactionExchangeId(null)
                ;

                $this->manager->persist($newTransaction);
                $this->manager->flush();
            }
        }


        // Update holdings
        $user = $account->getUser();
        if ($user instanceof User) {
            $this->manager->getRepository(Holding::class)->updateHoldings($user);
        }
    }
}
