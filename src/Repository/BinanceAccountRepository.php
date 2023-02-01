<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Account;
use App\Entity\BinanceAccount;
use App\Entity\User;
use Binance\API;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BinanceAccount>
 *
 * @method BinanceAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method BinanceAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method BinanceAccount[]    findAll()
 * @method BinanceAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BinanceAccountRepository extends AccountRepository
{
    private API $binanceApiConnexion;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BinanceAccount::class);

        $userAdmin    = $registry->getRepository(User::class)->findBy(['username' => 'moneyes']);
        $accountAdmin = $registry->getRepository(Account::class)->findOneBy(['user' => $userAdmin]);
        if ($accountAdmin instanceof Account) {
            $this->binanceApiConnexion = new API($accountAdmin->getPublicKey(), $accountAdmin->getPrivateKey());
        }
    }

    public function getSymbolsList(Account $account): array
    {
        $customerBinanceApi = $this->customerBinanceApi($account); // Connect to Binance API with customer's credentials
        $symbolsListRaw = $customerBinanceApi->exchangeInfo()['symbols'];
        $symbolsList = [];

        foreach ($symbolsListRaw as $symbol) {
            $symbolsList[] = $symbol['symbol'];
        }

        return $symbolsList;
    }

    /**
     * @throws \Exception
     */
    public function fetchTransactions(Account $account, ?\DateTime $previousUpdate = null): array
    {
        $customerBinanceApi = $this->customerBinanceApi($account); // Connect to Binance API with customer's credentials
        $symbolsList        = $this->getSymbolsList($account);
        $tradesList         = [];

        $index = 0;
        foreach ($symbolsList as $symbol) {
            if (0 !== $index % 20) {
                sleep(1);
            }
            $tradesList = array_merge($tradesList, $customerBinanceApi->history($symbol));
            ++$index;
        }

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
        $price              = (float) $customerBinanceApi->price($isoCode);

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

        $balances = $customerBinanceApi->balances();

        $assets = [];

        foreach ($balances as $asset => $balance) {
            $floatBalanceAssetBalance = (float)$balance['available'];
            if ($floatBalanceAssetBalance > 0) {
                $assets[] = $asset;
            }
        }

        return $assets;
    }

    public function getAccountInfo(Account $account): array
    {
        $entityManager = $this->getEntityManager(); // Init Entity Manager
        // Get Exchange's Holdings
        /* $exchangeHoldings = $entityManager->getRepository(Holding::class)->findBy([
            "user " => $user->getId(),
            //TODO : ajouter de récupérer par account
        ]);

        $value = 0;
        $percentage = 0;
        $gainLoss = 0; */

        return [
            $account->getExchange()->getLabel() => [
                'value' => $this->getTotalValue($account),
                'symbol' => 'USDT',
            ],
        ];
    }

    /**
     * @throws \Exception
     */
    public function getTotalValue(Account $account): float
    {
        $customerBinanceApi = $this->customerBinanceApi($account); // Connect to Binance API with customer's credentials
        $userAssets = $this->getAssets($account);
        $balances = $customerBinanceApi->balances();

        $totalValue = 0.0;

        foreach ($balances as $asset => $balance) {
            if (in_array($asset, $userAssets)) {
                $floatBalanceAssetBalance = (float)$balance['available'] + (float)$balance['onOrder'];
                try {
                    if ('USDT' === $asset) {
                        $price = 1;
                    } else {
                        $price = (float)$this->getPrice($account, $asset . 'USDT');
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

    private function customerBinanceApi(Account $account): API
    {
        return new API($account->getPublicKey(), $account->getPrivateKey());
    }
}
