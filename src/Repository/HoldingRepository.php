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
	 * Auto update Holding for a Transaction or create if not already created
	 *
	 * @param Transaction $transaction
	 *
	 * @return void
	 */
	public function upsertHolding( Transaction $transaction ): void {
		$entityManager = $this->getEntityManager();
		$idCurrency    = strval( $transaction->getIdCurrency()->getId() );
		$idExchange    = strval( $transaction->getIdExchange()->getId() );
		$idUser    = strval( $transaction->getIdUser()->getId() );
		$holdingToFind = $entityManager->getRepository( Holding::class )
		                               ->findOneBy( [
			                               "idCurrency" => $idCurrency,
			                               "idExchange" => $idExchange,
			                               "idUser" => $idUser,
		                               ] );
		if (!$holdingToFind) {
			# Create one
			$newHolding = new Holding();
			$newHolding->setIdCurrency($transaction->getIdCurrency())
			           ->setIdExchange($transaction->getIdExchange())
			           ->setIdUser($transaction->getIdUser())
			           ->setQuantity($transaction->getQuantity())
			           ->setAveragePurchasePrice($transaction->getPrice());
			$entityManager->persist($newHolding);
		} # If Holding not exists, create one
		else {
			$previousAveragePurchasePrice = $holdingToFind->getAveragePurchasePrice();
			$previousQuantity = $holdingToFind->getQuantity();
			$nextQuantity = $transaction->getQuantity();
			$transactionPrice = $transaction->getPrice();
			$totalQuantity = $previousQuantity + $nextQuantity;
			$nextAveragePurchasePrice = ((($previousQuantity * $previousAveragePurchasePrice)+($nextQuantity * $transactionPrice))/$totalQuantity);
			$holdingToFind->setQuantity(strval($totalQuantity))
			              ->setAveragePurchasePrice(strval($nextAveragePurchasePrice));
			$entityManager->persist($holdingToFind);
		} # If exists, update it

		$entityManager->flush();

	}


	/**
	 * Auto update all holdings of a given user
	 *
	 * @param User $user
	 *
	 * @return void
	 */
	public function updateHoldings(User $user): void{
		# Get all User's Transactions
		$entityManager = $this->getEntityManager(); # Init Entity Manager
		$transactions = $entityManager->getRepository(Transaction::class)->getTransactions($user); # Get all user's transactions
		foreach ($transactions as $transaction){
			$this->upsertHolding($transaction);
		} # For each Transaction, check Holding
	}

}
