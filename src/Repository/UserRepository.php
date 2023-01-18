<?php

declare( strict_types=1 );

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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find( $id, $lockMode = null, $lockVersion = null )
 * @method User|null findOneBy( array $criteria, array $orderBy = null )
 * @method User[]    findAll()
 * @method User[]    findBy( array $criteria, array $orderBy = null, $limit = null, $offset = null )
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface {
	public function __construct( ManagerRegistry $registry, EntityManagerInterface $entityManager ) {
		parent::__construct( $registry, User::class );
	}

	public function save( User $entity, bool $flush = false ): void {
		$this->getEntityManager()->persist( $entity );

		if ( $flush ) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove( User $entity, bool $flush = false ): void {
		$this->getEntityManager()->remove( $entity );

		if ( $flush ) {
			$this->getEntityManager()->flush();
		}
	}

	/**
	 * Used to upgrade (rehash) the user's password automatically over time.
	 */
	public function upgradePassword( PasswordAuthenticatedUserInterface $user, string $newHashedPassword ): void {
		if ( ! $user instanceof User ) {
			throw new UnsupportedUserException( sprintf( 'Instances of "%s" are not supported.', \get_class( $user ) ) );
		}

		$user->setPassword( $newHashedPassword );

		$this->save( $user, true );
	}




//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
