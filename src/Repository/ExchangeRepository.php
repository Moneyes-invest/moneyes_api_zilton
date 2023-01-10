<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Exchange;
use App\Entity\Holding;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exchange>
 *
 * @method Exchange|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exchange|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exchange[]    findAll()
 * @method Exchange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExchangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exchange::class);
    }

    public function save(Exchange $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Exchange $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

	public function getExchangePerf(Exchange $exchange, User $user): object|array|null {
		$entityManager = $this->getEntityManager(); # Init Entity Manager
		# Get Exchange's Holdings
		$exchangeHoldings = $entityManager->getRepository(Holding::class)->findBy(["idUser" => $user, "idExchange" => $exchange]);

		$value = 0;
		$percentage = 0;
		$gainLoss = 0;

		foreach ($exchangeHoldings as $exchangeHolding){
			$value += $exchangeHolding->getQuantity();
		}

		$exchangePerf = array(
			$exchange->getLabel() => array(
				"value" => $exchangeHoldings
			)
		);


		return $exchangeHoldings;
	}

}
