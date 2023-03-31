<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Binance\API;
use App\Binance\RateLimiter;
use App\Entity\Account;
use App\Entity\Asset;
use App\Entity\BinanceAccount;
use App\Entity\Holding;
use App\Entity\Symbol;
use App\Entity\Transaction;
use App\Entity\Transfer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use mysql_xdevapi\Exception;

/**
 * @method BinanceAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method BinanceAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method BinanceAccount[]    findAll()
 * @method BinanceAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BinanceAccountRepository extends AccountRepository // implements AccountInterface
{
    private API $binanceApiConnexion;

    public function __construct(EntityManagerInterface $manager, ManagerRegistry $registry)
    {
        parent::__construct($registry, BinanceAccount::class);

        $userAdmin    = $manager->getRepository(User::class)->findBy(['username' => 'moneyes']);
        $accountAdmin = $manager->getRepository(Account::class)->findOneBy(['user' => $userAdmin]);
        if ($accountAdmin instanceof Account) {
            $this->binanceApiConnexion = new API($accountAdmin->getPublicKey(), $accountAdmin->getPrivateKey());
        }
    }

    public function getSymbolsList(Account $account): array
    {
        if ('' === $account->getPublicKey() || '' === $account->getPrivateKey()) {
            throw new \Exception('No API credentials provided');
        }
        $customerBinanceApi = $this->customerBinanceApi($account); // Connect to Binance API with customer's credentials
        /**
         * @phpstan-ignore-next-line
         */
        $symbolsListRaw = $customerBinanceApi->exchangeInfo()['symbols'];
        $symbolsList    = [];

        foreach ($symbolsListRaw as $symbol) {
            $symbolsList[] = $symbol['symbol'];
        }

        return $symbolsList;
    }

    /**
     * @throws \Exception
     */
    public function fetchTransactions(Account $account, ?array $symbolsBalance = null, ?bool $new = false): array
    {
        $customerBinanceApi = $this->customerBinanceApi($account); // Connect to Binance API with customer's credentials

        if (null !== $symbolsBalance) {
            $symbolsList = $symbolsBalance;
        } // If the list of symbols is provided, use it
        else {
            $symbolsList = $this->getSymbolsList($account);
        } // Get the list of symbols

        $tradesList = [];
        $total      = count($symbolsList);
        $fetched    = 0;

        foreach ($symbolsList as $symbol) {
            if ($new) {
                $symbolEntity    = $this->getEntityManager()->getRepository(Symbol::class)->findOneBy(['code' => $symbol]);
                $lastTransaction = $this->getEntityManager()->getRepository(Transaction::class)->findOneBy(['account' => $account, 'symbol' => $symbolEntity], ['date' => 'DESC']);
                if ($lastTransaction instanceof Transaction) {
                    $latestTransactionId = $lastTransaction->getExternalTransactionId();
                    try {
                        /**
                         * @phpstan-ignore-next-line
                         */
                        $tradesList = array_merge($tradesList, $customerBinanceApi->history($symbol, 500, $latestTransactionId));
                    } catch (Exception $exception) {
                        throw new \Exception('problème dans le tradelist '.$symbol);
                    }
                }
            } else {
                /**
                 * @phpstan-ignore-next-line
                 */
                $tradesList = array_merge($tradesList, $customerBinanceApi->history($symbol)); // Get all trades for each symbol
                $remaining  = $total - $fetched;
                echo 'Fetched transactions for '.$symbol.' | '.$fetched.'/'.$total.' => '.$remaining.' remaining.'.PHP_EOL;
            }
            ++$fetched;
        } // Get all trades for each symbol

        return $tradesList;
    }

    /**
     * Get Price of an asset by Binance API.
     *
     * @throws \Exception
     */
    public function getPrice(Account $account, string $isoCode): float
    {
        $customerBinanceApi = $this->customerBinanceApi($account); // Connect to Binance API with customer's credentials
        /**
         * @phpstan-ignore-next-line
         */
        $price = (float) $customerBinanceApi->price($isoCode);

        return $price;
    }

    /**
     * Get Price of assets by Binance API.
     *
     * @throws \Exception
     */
    public function getPrices(Account $account): array
    {
        $customerBinanceApi = $this->customerBinanceApi($account); // Connect to Binance API with customer's credentials

        return [
            [
                /**
                 * @phpstan-ignore-next-line
                 */
                $customerBinanceApi->prices(),
            ],
        ];
    }

    /**
     * Get Price of assets by Binance API.
     *
     * @throws \Exception
     */

    /**
     * Get all assets about a given customer SPOT account.
     *
     * @throws \Exception
     */
    public function getAssets(Account $account): array
    {
        $customerBinanceApi = $this->customerBinanceApi($account); // Connect to Binance API with customer's credentials
        /**
         * @phpstan-ignore-next-line
         */
        $balances = $customerBinanceApi->balances();

        $assets = [];

        foreach ($balances as $asset => $balance) {
            $floatBalanceAssetBalance = (float) $balance['available'] + (float) $balance['onOrder'];
            $assetEntity              = $this->getEntityManager()->getRepository(Asset::class)->findOneBy(['code' => $asset]);
            $previousBalance          = $this->getEntityManager()->getRepository(Holding::class)->findOneBy(['account' => $account, 'asset' => $assetEntity], ['date' => 'DESC']);
            if (!$previousBalance instanceof Holding) {
                $previousBalance = 0;
            } else {
                $previousBalance = $previousBalance->getQuantity();
            }
            if ($floatBalanceAssetBalance > 0) {
                $assets[] = [
                    'asset'           => $asset,  // Asset's ISO code
                    'balance'         => $floatBalanceAssetBalance, // Asset's balance
                    'previousBalance' => $previousBalance, // Asset's balance
                ];
            }
        }

        return $assets;
    }

    /**
     * @throws \Exception
     */
    public function getTotalValue(Account $account): float
    {
        $customerBinanceApi = $this->customerBinanceApi($account); // Connect to Binance API with customer's credentials
        $userAssets         = $this->getAssets($account);
        /**
         * @phpstan-ignore-next-line
         */
        $balances = $customerBinanceApi->balances();

        $totalValue = 0.0;

        foreach ($balances as $asset => $balance) {
            if (in_array($asset, $userAssets)) {
                $floatBalanceAssetBalance = (float) $balance['available'] + (float) $balance['onOrder'];
                try {
                    if ('USDT' === $asset) {
                        $price = 1;
                    } else {
                        $price = (float) $this->getPrice($account, $asset.'USDT');
                    }
                    $totalValue += $floatBalanceAssetBalance * $price;
                } catch (\Exception $exception) {
                    continue;
                }
            }
        }

        return $totalValue;
    }

    public function getBinanceApiConnexion(): API
    {
        return $this->binanceApiConnexion;
    }

    /**
     * @throws \Exception
     */
    public function getAccountSymbols(Account $account): array
    {
        $accountAssets = $this->getAssets($account); // user holdings assets
        $symbolsList   = $this->getSymbolsList($account); // all symbols list

        $accountSymbols = []; // symbolsBalance

        foreach ($symbolsList as $symbol) {
            // check if symbol string contains at least one of balance
            foreach ($accountAssets as $asset) {
                // if symbol contains balance at the beginning of the string
                if (str_starts_with($symbol, $asset['asset'])) {
                    // add symbol to symbolsBalance
                    $accountSymbols[] = $symbol; // add symbol to symbolsBalance
                } // if symbol contains balance at the beginning of the string
            } // foreach balance
        } // get symbolsBalance

        return array_unique($accountSymbols); // return symbolsBalance
    }

    /**
     * @throws \Exception
     */
    public function getAllSymbols(Account $account): array
    {
        $customerBinanceApi = $this->customerBinanceApi($account); // Connect to Binance API with customer's credentials
        /**
         * @phpstan-ignore-next-line
         */
        $rawSymbolsList = $customerBinanceApi->exchangeInfo()['symbols'];
        $symbolsList    = [];

        foreach ($rawSymbolsList as $symbol) {
            $symbolsList[] = $symbol['symbol'];
        }

        return $symbolsList;
    }

    /**
     * @throws \Exception
     */
    public function fetchTransfers(Account $account, ?bool $new = false): void
    {
        $threeMonthsInMs            = 7776000000;
        $todayTimestampMs           = time() * 1000;
        $start                      = $todayTimestampMs - $threeMonthsInMs;
        $end                        = $todayTimestampMs;
        $binanceCreationTimestampMs = 1498860000000;
        $withdrawHistory            = [];
        $depositHistory             = [];
        $customerBinanceApi         = $this->customerBinanceApi($account); // Connect to Binance API with customer's credentials
        $limit                      = $binanceCreationTimestampMs;

        if ($new) {
            $lastTransfer = $this->getEntityManager()->getRepository(Transfer::class)->findOneBy(['account' => $account], ['date' => 'DESC']);
            if (null !== $lastTransfer) {
                /**
                 * @phpstan-ignore-next-line
                 *
                 * //TODO: set Date from Transfer to not null
                 */
                $latestDate = $lastTransfer->getDate()->getTimestamp() * 1000;
                if ($latestDate > $start) {
                    $start = $latestDate + 1;
                }
                $limit = $latestDate;
            }
        }

        while ($start > $limit) {
            $params              = [];
            $params['startTime'] = $start;
            $params['endTime']   = $end;
            /**
             * @phpstan-ignore-next-line
             */
            $withdraw = $customerBinanceApi->withdrawHistory(null, $params);
            if (!empty($withdraw['withdrawList'])) {
                foreach ($withdraw['withdrawList'] as $withdrawValue) {
                    $withdrawHistory[] = $withdrawValue;
                }
            }
            // same for deposit
            /**
             * @phpstan-ignore-next-line
             */
            $deposit = $customerBinanceApi->depositHistory(null, $params);
            if (!empty($deposit)) {
                foreach ($deposit as $depositValue) {
                    $depositHistory[] = $depositValue;
                }
            }

            $start = $start - $threeMonthsInMs;
            $end   = $end - $threeMonthsInMs;
        }
        // register transactions withdraws
        foreach ($withdrawHistory as $withdraw) {
            $this->registerTransfer($withdraw, $account, 'applyTime', 'WITHDRAW');
        }

        // register transactions deposits
        foreach ($depositHistory as $deposit) {
            $this->registerTransfer($deposit, $account, 'insertTime', 'DEPOSIT');
        }
    }

    /**
     * @throws \Exception
     */
    public function registerTransfer(array $transferValue, Account $account, string $time, string $type): void
    {
        $transfer = new Transfer();
        $transfer->setAccount($account);
        $transfer->setQuantity((float) $transferValue['amount']);
        $existingTransfer = $this->getEntityManager()->getRepository(Transfer::class)->findOneBy(['externalTransferId' => $transferValue['id']]);
        if (null !== $existingTransfer) {
            return;
        } // if transfer already exists
        $transfer->setExternalTransferId($transferValue['id']);
        if ('applyTime' === $time) {
            $transfer->setDate(new \DateTime((string) $transferValue[$time]));
        } else {
            $date = new \DateTime();
            $date->setTimestamp((int) ($transferValue[$time] / 1000));
            $transfer->setDate(new \DateTime($date->format('Y-m-d H:i:s')));
        }

        $transfer->setType($type);
        $code  = strtolower($transferValue['coin']);
        $asset = $this->getEntityManager()->getRepository(Asset::class)->findOneBy(['code' => $code]);
        if (null === $asset) {
            $asset = new Asset();
            $asset->setCode($code);
            $asset->setId($code);
            $this->getEntityManager()->persist($asset);
            $this->getEntityManager()->flush();
        }
        $transfer->setAsset($asset);
        if ('DEPOSIT' === $type) {
            $transfer->setFees(0);
        } else {
            $transfer->setFees((float) $transferValue['transactionFee']);
        }
        $transfer->setAssetFees($asset);

        $this->getEntityManager()->persist($transfer);
        $this->getEntityManager()->flush();
    }

    /**
     * @throws \Exception
     */
    public function registerTransaction(array $transactions, Account $account, EntityManagerInterface $manager): void
    {
        // Register transactions
        foreach ($transactions as $transaction) {
            // Code ISO
            $codeIso = $transaction['symbol'];

            // If symbol is not in the database, create it
            $symbol = $manager->getRepository(Symbol::class)->findOneBy(['code' => $codeIso]);
            if (null === $symbol) {
                $newSymbol = new Symbol();
                $newSymbol->setCode($codeIso);
                $symbol = $newSymbol;
                $manager->persist($symbol);
                $manager->flush();
            }

            // Date
            $date = new \DateTime();
            $date->setTimestamp((int) ($transaction['time'] / 1000));

            // Order Direction
            if ($transaction['isBuyer']) {
                $orderDirection = 'BUY';
            } else {
                $orderDirection = 'SELL';
            }

            // Check if transaction already exists
            $transactionExists = $manager->getRepository(Transaction::class)->findOneBy(['externalTransactionId' => $transaction['id']]);
            if (null !== $transactionExists) {
                continue;
            }

            $transactionPrice      = (float) $transaction['price'];
            $transactionQuantity   = (float) $transaction['qty'];
            $externalTransactionId = (string) $transaction['id'];

            // Find asset of symbol transaction
            $asset = $manager->getRepository(Symbol::class)->findAsset($symbol->getCode());

            // if (null === $transactionExists && $binanceExchange instanceof Exchange) {
            $newTransaction = new Transaction();
            $newTransaction->setAccount($account)
                ->setSymbol($symbol)
                ->setDate(new \DateTime($date->format('Y-m-d H:i:s')))
                ->setOrderDirection($orderDirection)
                ->setPrice($transactionPrice)
                ->setQuantity($transactionQuantity)
                ->setAsset($asset)
                ->setExternalTransactionId($externalTransactionId);

            $manager->persist($newTransaction);
            $manager->flush();
            // }
        }
    }

    /**
     * @throws \Exception
     */
    public function customerBinanceApi(Account $account): RateLimiter
    {
        $api = new API($account->getPublicKey(), $account->getPrivateKey());

        return new RateLimiter($api);
    }
}
