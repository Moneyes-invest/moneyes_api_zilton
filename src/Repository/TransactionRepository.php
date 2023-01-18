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

    public function getTransactions(User $user, ?Exchange $exchange = null, ?Currency $currency = null, ?\DateTime $latestUpdate = null): array
    {
        $entityManager = $this->getEntityManager();
        // 1. if !Exchange and !Currency
        if (!$exchange && !$currency) {
            if (!$latestUpdate) {
                $transactions = $entityManager->getRepository(Transaction::class)
                                               ->findBy([
                                                   'user' => $user,
                                               ]);
            } else {
                $queryAllNewTransactions = $entityManager
                    ->createQuery('SELECT t FROM App\Entity\Transaction t WHERE t.date > :latestUpdate ORDER BY t.date DESC')
                    ->setParameter('latestUpdate', $latestUpdate);
                $transactions = $queryAllNewTransactions->getResult();
            }
        }
        // 2. if Exchange and !Currency
        elseif (!$currency) {
            if (!$latestUpdate) {
                $transactions = $entityManager->getRepository(Transaction::class)
                                              ->findBy([
                                                  'user'     => $user,
                                                  'exchange' => $exchange,
                                              ]);
            } else {
                $queryParameters = new ArrayCollection([
                    new Parameter('latestUpdate'),
                    new Parameter('idExchange'),
                ]);
                $queryAllNewTransactions = $entityManager
                    ->createQuery('SELECT t FROM App\Entity\Transaction t WHERE t.date > :latestUpdate AND t.exchange > :exchange ORDER BY t.date DESC')
                    ->setParameters($queryParameters);
                $transactions = $queryAllNewTransactions->getResult();
            }
        }
        // 3. if Exchange and Currency
        else {
            if (!$latestUpdate) {
                $transactions = $entityManager->getRepository(Transaction::class)
                                              ->findBy([
                                                  'user'       => $user,
                                                  'idExchange' => $exchange,
                                                  'idCurrency' => $currency,
                                              ]);
            } else {
                $queryParameters = new ArrayCollection([
                    new Parameter('latestUpdate'),
                    new Parameter('idExchange'),
                    new Parameter('idCurrency'),
                ]);
                $queryAllNewTransactions = $entityManager
                    ->createQuery('SELECT t FROM App\Entity\Transaction t WHERE t.date > :latestUpdate AND t.exchange = :exchange AND t.idCurrency = idCurrency ORDER BY t.date DESC')
                    ->setParameters($queryParameters);
                $transactions = $queryAllNewTransactions->getResult();
            }
        }

        return (array) $transactions;
    }
}
