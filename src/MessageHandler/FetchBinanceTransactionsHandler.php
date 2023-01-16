<?php

namespace App\MessageHandler;

use App\Entity\Account;
use App\Entity\BinanceAccount;
use App\Entity\Currency;
use App\Entity\Exchange;
use App\Entity\Holding;
use App\Entity\Transaction;
use App\Message\FetchBinanceTransactions;
use App\Repository\BinanceAccountRepository;
use App\Repository\HoldingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function Symfony\Component\Translation\t;
use const http\Client\Curl\Versions\CURL;

#[AsMessageHandler]
class FetchBinanceTransactionsHandler
{

    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    public function __invoke(FetchBinanceTransactions $fetchBinanceTransactions)
    {
        $account = $this->manager->getRepository(Account::class)->find($fetchBinanceTransactions->getAccountId());

        $transactions = $this->manager->getRepository(BinanceAccount::class)->fetchTransactions($account, $fetchBinanceTransactions->getPreviousUpdate());

        #Binance ID
        $binanceId = $this->manager->getRepository(Exchange::class)->findOneBy(["label" => "Binance"]);


        # Register transactions
        foreach ($transactions as $transaction) {
            #Code ISO
            $codeIso = $transaction["symbol"];

            #Currency ID
            $currencyId = $this->manager->getRepository(Currency::class)->findOneBy(["codeIso" => $codeIso]);
            if ($currencyId == null) {
                $newCurrency = new Currency();
                $newCurrency->setCodeIso($codeIso)->setName($codeIso);
                $currencyId = $newCurrency;
                $this->manager->persist($currencyId);
                $this->manager->flush();
            }

            #Date
            $date = new \DateTime();
            $date->setTimestamp($transaction["time"]);

            #Order Direction
            if ($transaction["isBuyer"]) {
                $orderDirection = "BUY";
            } else {
                $orderDirection = "SELL";
            }

            #Transaction ID
            $exchangeTradeId = $transaction["id"];
            $transactionExists = $this->manager->getRepository(Transaction::class)->findOneBy(["exchangeId" => $exchangeTradeId]);

            if ($transactionExists == null) {
                $newTransaction = new Transaction();
                $newTransaction->setIdExchange($binanceId)
                    ->setIdUser($account->getIdUser())
                    ->setIdCurrency($currencyId)
                    ->setDate($date)
                    ->setOrderDirection($orderDirection)
                    ->setPrice($transaction["price"])
                    ->setQuantity($transaction["qty"])
                    ->setExchangeId($transaction["id"]);

                $this->manager->persist($newTransaction);
                $this->manager->flush();
            }
        }

        # Update holdings
        $this->manager->getRepository(Holding::class)->updateHoldings($account->getIdUser());

    }
}
