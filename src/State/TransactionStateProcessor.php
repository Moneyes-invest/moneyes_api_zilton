<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Holding;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TransactionStateProcessor implements ProcessorInterface
{
	public function __construct(private readonly EntityManagerInterface $manager) {
	}

	public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed {

		# EntityManager
		$entityManager = $this->manager;
		$entityManager->getRepository(Holding::class)->upsertHolding($data);

		$entityManager->persist($data);
		$entityManager->flush();

		return $data;

	}
}
