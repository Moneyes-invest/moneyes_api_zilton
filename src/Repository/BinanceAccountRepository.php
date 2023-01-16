<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\BinanceAccount;
use App\Entity\Currency;
use App\Entity\Exchange;
use App\Entity\Holding;
use App\Entity\User;
use Binance\API;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use function Symfony\Component\Translation\t;

/**
 * @extends ServiceEntityRepository<BinanceAccount>
 *
 * @method BinanceAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method BinanceAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method BinanceAccount[]    findAll()
 * @method BinanceAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BinanceAccountRepository extends ServiceEntityRepository
{

    private API $binanceApiConnexion;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BinanceAccount::class);

        $userAdmin = $registry->getRepository(User::class)->findBy(["username" => "moneyes"]);
        $accountAdmin = $registry->getRepository(Account::class)->findOneBy(["idUser" => $userAdmin]);

        $this->binanceApiConnexion = new API($accountAdmin->getPublicKey(), $accountAdmin->getPrivateKey());
    }

    public function save(BinanceAccount $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BinanceAccount $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Init \Binance\API Object with Customer credentials
     *
     * @param Account $account
     *
     * @return API
     */
    private function customerBinanceApi(Account $account): API
    {
        return new API($account->getPublicKey(), $account->getPrivateKey());
    }

    public function getSymbolsList(Account $account)
    {
        $customerBinanceApi = $this->customerBinanceApi($account); # Connect to Binance API with customer's credentials

        $symbolsListRaw = $customerBinanceApi->exchangeInfo()["symbols"];

        $symbolsList = array();


        foreach ($symbolsListRaw as $symbol) {
            $symbolsList[] = $symbol["symbol"];
        }


        return $symbolsList;
    }

    /**
     * @throws Exception
     */
    public function fetchTransactions(Account $account, ?DateTime $previousUpdate = null): array
    {

        $customerBinanceApi = $this->customerBinanceApi($account); # Connect to Binance API with customer's credentials
        $customerAssets = $this->getAssets($account);
        $symbolsList = $this->getSymbolsList($account);

        $tradesList = array();

        $symbolsListFiltered = array();

        /*
        foreach ($customerAssets as $customerAsset) {
            $tempArray = array_filter($symbolsList, static function ($asset) use ($customerAsset) {
                if (str_starts_with($asset, $customerAsset)) {
                    return $asset;
                }
            });
            $symbolsListFiltered = array_merge($symbolsListFiltered, $tempArray);
        }*/

        $i = 0;

        foreach ($symbolsList as $symbol) {
            if ($i % 20 != 0) {
                sleep(1);
            }
            $tradesList = array_merge($tradesList, $customerBinanceApi->history($symbol));
            $i++;
        }

        return $tradesList;
    }

    /**
     * Get Price of an asset by Binance API
     *
     * @throws Exception
     */
    public function getPrice(Account $account, string $isoCode): float
    {
        $customerBinanceApi = $this->customerBinanceApi($account); # Connect to Binance API with customer's credentials
        $price = (float)$customerBinanceApi->price($isoCode);
        return $price;
    }

    /**
     * Get Price of assets by Binance API
     *
     * @throws Exception
     */
    public function getPrices(Account $account): array
    {
        $customerBinanceApi = $this->customerBinanceApi($account); # Connect to Binance API with customer's credentials

        return array(
            array(
                $customerBinanceApi->prices(),
            )
        );
    }

    /**
     * Get Price of assets by Binance API
     *
     * @throws Exception
     */
    public function test(Account $account): array
    {
        $manager = $this->getEntityManager();
        $customerBinanceApi = $this->customerBinanceApi($account); # Connect to Binance API with customer's credentials
        $user = $account->getIdUser();
        $manager->getRepository(Holding::class)->updateHoldings($user);
        $holdings = $manager->getRepository(Holding::class)->findBy(["idUser" => $user]);
        return array(
            $holdings
        );
    }

    /**
     * Get all assets about a given customer SPOT account
     *
     * @param Account $account
     *
     * @return array
     * @throws Exception
     */
    public function getAssets(Account $account): array
    {
        $customerBinanceApi = $this->customerBinanceApi($account); # Connect to Binance API with customer's credentials

        $balances = $customerBinanceApi->balances();

        $assets = array();

        foreach ($balances as $asset => $balance) {
            $floatBalanceAssetBalance = (float)$balance["available"];
            if ($floatBalanceAssetBalance > 0) {
                $assets[] = $asset;
            }
        }

        return $assets;
    }

    public function getAccountPerf(Account $account, User $user): object|array|null
    {
        $entityManager = $this->getEntityManager(); # Init Entity Manager
        # Get Exchange's Holdings
        $exchangeHoldings = $entityManager->getRepository(Holding::class)->findBy([
            "idUser" => $user,
            //TODO : ajouter de récupérer par account
        ]);

        $value = 0;
        $percentage = 0;
        $gainLoss = 0;


        $exchangePerf = array(
            $account->getIdExchange()->getLabel() => array(
                "value" => $entityManager->getRepository(BinanceAccount::class)->getTotalValue($account),
                "symbol" => "USDT",
            )
        );


        return $exchangePerf;
    }

    /**
     * @throws Exception
     */
    public function getTotalValue(Account $account): float
    {
        $customerBinanceApi = $this->customerBinanceApi($account); # Connect to Binance API with customer's credentials
        $userAssets = $this->getAssets($account);
        $balances = $customerBinanceApi->balances();

        $totalValue = 0.0;

        foreach ($balances as $asset => $balance) {
            if (in_array($asset, $userAssets)) {
                $floatBalanceAssetBalance = (float)$balance["available"] + (float)$balance["onOrder"];
                try {
                    if ($asset == "USDT") {
                        $price = 1;
                    } else {
                        $price = (float)$this->getPrice($account, $asset . "USDT");
                    }
                    $totalValue += $floatBalanceAssetBalance * $price;
                } catch (Exception $exception) {
                }
            }
        }
        return $totalValue;
    }

}
