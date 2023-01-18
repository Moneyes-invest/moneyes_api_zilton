<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Holding;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;

class TransactionStateProcessor implements ProcessorInterface
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?Transaction
    {
        // EntityManager
        $entityManager = $this->manager;
        if (!$data instanceof Transaction) {
            return null;
        }
        $entityManager->getRepository(Holding::class)->upsertHolding($data);

        $entityManager->persist($data);
        $entityManager->flush();

        return $data;
    }
}
