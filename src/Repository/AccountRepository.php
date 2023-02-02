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
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Account>
 *
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass = Account::class)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * Delete Transactions from a User Account.
     */
    public function flushTransactions(Account $account): void
    {
        $user         = $account->getUser();
        $transactions = $this->getEntityManager()->getRepository(Transaction::class)->findBy(['user' => $user]);

        foreach ($transactions as $transaction) {
            $this->getEntityManager()->remove($transaction);
        }

        $this->getEntityManager()->flush();
    }
}
