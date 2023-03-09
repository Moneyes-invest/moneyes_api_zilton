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
use App\Entity\Holding;
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

    public function getBalance(Account $account): array
    {
        $balance = [];
        // Get all holdings order by date DESC LIMIT 1
        $holdings = $this->getEntityManager()->getRepository(Holding::class)->findBy(['account' => $account], ['date' => 'DESC'], 1);
        foreach ($holdings as $holding) {
            if (!$holding instanceof Holding) {
                continue;
            }
            $balance[] = [
                'asset'   => $holding->getAsset()->getCode(),
                'balance' => $holding->getQuantity(),
            ];
        }

        return $balance;
    }
}
