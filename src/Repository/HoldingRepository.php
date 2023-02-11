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
        $symbol        = $transaction->getSymbol();
        $account       = $transaction->getAccount();
        $holdingToFind = $entityManager->getRepository(Holding::class)
                                       ->findOneBy([
                                           'symbol'     => $symbol,
                                           'account'    => $account,
                                       ]);

        if (!$holdingToFind) {
            // Create one
            $newHolding = new Holding();
            // TODO: Get Asset from Symbol
            $newHolding->setAsset($transaction->getSymbol())
                       ->setAccount($transaction->getAccount())
                       ->setQuantity(floatval($transaction->getQuantity()));
            $entityManager->persist($newHolding);
        } // If Holding not exists, create one
        elseif ('TRANSFERT' === $transaction->getOrderDirection()) {
            $previousQuantity             = $holdingToFind->getQuantity();
            $nextQuantity                 = $previousQuantity - $transaction->getQuantity();
            $holdingToFind->setQuantity(floatval($nextQuantity));
            $entityManager->persist($holdingToFind);
        } else {
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
        }
        $entityManager->flush();

        // Get all User's Transactions
        $transactions = $entityManager->getRepository(Transaction::class)->getTransactions($user); // Get all user's transactions

        foreach ($transactions as $transaction) {
            $this->upsertHolding($transaction);
        } // For each Transaction, check Holding
    }
}
