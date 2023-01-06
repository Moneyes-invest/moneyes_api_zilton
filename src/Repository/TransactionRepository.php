<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Currency;
use App\Entity\Exchange;
use App\Entity\Holding;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function save(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

	public function getTransactions(User $user, ?Exchange $exchange, ?Currency $currency, ?\DateTime $latestUpdate){
		$entityManager = $this->getEntityManager();
		# 1. if !Exchange and !Currency
		if (!$exchange && !$currency){
			if (!$latestUpdate){
				$transactions = $entityManager->getRepository( Transaction::class )
				                               ->findBy( [
					                               "idUser" => $user,
				                               ] );
			}
			else{
				$queryAllNewTransactions = $entityManager
					->createQuery( 'SELECT t FROM App\Entity\Transaction t WHERE t.date > :latestUpdate ORDER BY t.date DESC' )
					->setParameter('latestUpdate', $latestUpdate);
				$transactions = $queryAllNewTransactions->getResult();
			}
		}
		# 2. if Exchange and !Currency
		elseif (!$currency){
			if (!$latestUpdate){
				$transactions = $entityManager->getRepository( Transaction::class )
				                              ->findBy( [
					                              "idUser" => $user,
					                              "idExchange" => $exchange,
				                              ] );
			}
			else{
				$queryParameters = new ArrayCollection(array(
					new Parameter('latestUpdate', $latestUpdate),
					new Parameter('idExchange', $exchange->getId()),
				));
				$queryAllNewTransactions = $entityManager
					->createQuery( 'SELECT t FROM App\Entity\Transaction t WHERE t.date > :latestUpdate AND t.idExchange > :idExchange ORDER BY t.date DESC' )
					->setParameters($queryParameters);
				$transactions = $queryAllNewTransactions->getResult();
			}
		}
		# 3. if Exchange and Currency
		else{
			if (!$latestUpdate){
				$transactions = $entityManager->getRepository( Transaction::class )
				                              ->findBy( [
					                              "idUser" => $user,
					                              "idExchange" => $exchange,
					                              "idCurrency" => $currency,
				                              ] );
			}
			else{
				$queryParameters = new ArrayCollection(array(
					new Parameter('latestUpdate', $latestUpdate),
					new Parameter('idExchange', $exchange->getId()),
					new Parameter('idCurrency', $currency->getId()),
				));
				$queryAllNewTransactions = $entityManager
					->createQuery( 'SELECT t FROM App\Entity\Transaction t WHERE t.date > :latestUpdate AND t.idExchange = :idExchange AND t.idCurrency = idCurrency ORDER BY t.date DESC' )
					->setParameters($queryParameters);
				$transactions = $queryAllNewTransactions->getResult();
			}
		}
		return $transactions;
	}

//    /**
//     * @return Transaction[] Returns an array of Transaction objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Transaction
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
