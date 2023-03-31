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
use App\Entity\Asset;
use App\Entity\Symbol;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Symbol>
 *
 * @method Symbol|null find($id, $lockMode = null, $lockVersion = null)
 * @method Symbol|null findOneBy(array $criteria, array $orderBy = null)
 * @method Symbol[]    findAll()
 * @method Symbol[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SymbolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Symbol::class);
    }

    public function save(Symbol $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Symbol $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws \Exception
     */
    public function findAsset(string $codeSymbol): ?Asset
    {
        $base = $this->getAssetAndQuoteFromSymbol($codeSymbol)['base'];
        $base = strtolower($base);

        return $this->getEntityManager()->getRepository(Asset::class)->findOneBy(['code' => $base]);
    }

    /**
     * @throws \Exception
     */
    public function getAssetAndQuoteFromSymbol(string $symbol): array
    {
        $api          = new API('', '');
        $exchangeInfo = $api->exchangeInfo();

        return [
            'base'  => $exchangeInfo['symbols'][$symbol]['baseAsset'],
            'quote' => $exchangeInfo['symbols'][$symbol]['quoteAsset'],
        ];
    }
}
