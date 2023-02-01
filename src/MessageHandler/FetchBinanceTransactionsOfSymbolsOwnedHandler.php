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
class FetchBinanceTransactionsOfSymbolsOwnedHandler
{
    /**
     * @param EntityManagerInterface $manager
     */
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

        // Fetch transactions for owned symbols
        $transactions = $this->manager->getRepository(BinanceAccount::class)->fetchTransactions($account, $accountSymbols, $fetchBinanceTransactions->getPreviousUpdate());

        // Binance ID
        $binanceExchange = $this->manager->getRepository(Exchange::class)->findOneBy(['label' => 'Binance']);

        //Save transactions into database
        $fetchBinanceTransactions->saveTransactions($transactions, $account, $binanceExchange, $this->manager);

    }
}
