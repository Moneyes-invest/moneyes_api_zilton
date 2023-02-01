<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Holding;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Holding>
 *
 * @method Holding|null find($id, $lockMode = null, $lockVersion = null)
 * @method Holding|null findOneBy(array $criteria, array $orderBy = null)
 * @method Holding[]    findAll()
 * @method Holding[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HoldingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Holding::class);
    }

    public function save(Holding $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Holding $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Auto update Holding for a Transaction or create if not already created.
     */
    public function upsertHolding(Transaction $transaction): void
    {
        $entityManager = $this->getEntityManager();
        $currency    = $transaction->getCurrency();
        $exchange    = $transaction->getExchange();
        $user          = $transaction->getUser();
        $holdingToFind = $entityManager->getRepository(Holding::class)
                                       ->findOneBy([
                                           'currency' => $currency,
                                           'exchange'   => $exchange,
                                           'user'      => $user,
                                       ]);

        if (!$holdingToFind) {
            // Create one
            $newHolding = new Holding();
            $newHolding->setCurrency($transaction->getCurrency())
                       ->setExchange($transaction->getExchange())
                       ->setUser($transaction->getUser())
                       ->setQuantity(floatval($transaction->getQuantity()))
                       ->setAveragePurchasePrice(floatval($transaction->getPrice()));
            $entityManager->persist($newHolding);
        } // If Holding not exists, create one
        else {
            $previousAveragePurchasePrice = $holdingToFind->getAveragePurchasePrice();
            $previousQuantity             = $holdingToFind->getQuantity();
            $nextQuantity                 = $transaction->getQuantity();
            $transactionPrice             = $transaction->getPrice();
            $totalQuantity                = $previousQuantity + $nextQuantity;
            $nextAveragePurchasePrice     = ((($previousQuantity * $previousAveragePurchasePrice) + ($nextQuantity * $transactionPrice)) / $totalQuantity);
            $holdingToFind->setQuantity(floatval($totalQuantity))
                          ->setAveragePurchasePrice(floatval($nextAveragePurchasePrice));
            $entityManager->persist($holdingToFind);
        } // If exists, update it

        $entityManager->flush();
    }

    /**
     * Auto update all holdings of a given user.
     */
    public function updateHoldings(User $user): void
    {
        $entityManager = $this->getEntityManager(); // Init Entity Manager
        // Remove all User Holdings
        $holdings = $entityManager->getRepository(Holding::class)->findBy(['user' => $user]);

        if ($holdings) {
            foreach ($holdings as $holding) {
                $entityManager->remove($holding);
            }
            $entityManager->flush();
        }

        // Get all User's Transactions
        $transactions = $entityManager->getRepository(Transaction::class)->getTransactions($user); // Get all user's transactions

        foreach ($transactions as $transaction) {
            $this->upsertHolding($transaction);
        } // For each Transaction, check Holding
    }
}
