<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\BinanceAccount;
use App\Entity\Currency;
use App\Entity\User;
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

    private \Binance\API $binanceApiConnexion;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BinanceAccount::class);

        $userAdmin = $registry->getRepository(User::class)->findBy(["username" => "moneyes"]);
        $accountAdmin = $registry->getRepository(Account::class)->findOneBy(["idUser" => $userAdmin]);

        $this->binanceApiConnexion = new \Binance\API($accountAdmin->getPublicKey(), $accountAdmin->getPrivateKey());
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
     * @return \Binance\API
     */
    private function customerBinanceApi(Account $account): \Binance\API
    {
        return new \Binance\API($account->getPublicKey(), $account->getPrivateKey());
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
    public function fetchTransactions(Account $account, ?\DateTime $previousUpdate = null): array
    {

        $customerBinanceApi = $this->customerBinanceApi($account); # Connect to Binance API with customer's credentials
        $customerAssets = $this->getAssets($account);
        $symbolsList = $this->getSymbolsList($account);

        $tradesList = array();
        $i = 0;

        $symbolsListFiltered = array();

        foreach ($customerAssets as $customerAsset) {
            $tempArray = array_filter($symbolsList, static function ($asset) use ($customerAsset) {
                if (strpos($asset, $customerAsset)) {
                    return $asset;
                }
            });
            $symbolsListFiltered = array_merge($symbolsListFiltered, $tempArray);
        }

        foreach ($symbolsListFiltered as $symbolFiltered) {
            if ($i == 20)
                break;
            $tradesList[] = $customerBinanceApi->history($symbolFiltered);
            $i++;
        }

        return array(
            $tradesList
        );
    }

    /**
     * Get Price of an asset by Binance API
     *
     * @throws Exception
     */
    public function getPrice(Account $account, string $isoCode): array
    {
        $customerBinanceApi = $this->customerBinanceApi($account); # Connect to Binance API with customer's credentials

        return array(
            array(
                $isoCode => $customerBinanceApi->price($isoCode),
            )
        );
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
        $symbolsList = $this->getSymbolsList($account);
        return $symbolsList;
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

}
