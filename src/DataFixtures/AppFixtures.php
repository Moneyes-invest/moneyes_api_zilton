<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\Currency;
use App\Entity\Exchange;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;

    /**
     * Init Fixture Class.
     */
    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
		##### USERS #####
        # Create customer
        $userCustomer = new User();
        $userCustomer->setEmail($this->faker->email())
                      ->setUsername($this->faker->userName())
                      ->setName($this->faker->name())
                      ->setRoles(['ROLE_USER'])
                      ->setLastname($this->faker->lastName())
                      ->setPassword('$2y$13$Avfr0GAnTYFWtBdm7lOi3eiZK0.frdZ4hjV2aBAu7gfdg2QFLy.EK');
        $manager->persist($userCustomer);

        # Create admin account
        $userAdmin = new User();
        $userAdmin->setEmail('admin@moneyes.fr')
                   ->setUsername('moneyes')
                   ->setName('Moneyes')
                   ->setLastname('Invest')
                   ->setPassword('$2y$13$ZfiM3kZ/EnILTe9zeoK29.LwVy2VgWp2H89zR9C.HSDCoggcZhouS')
                   ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $manager->persist($userAdmin);


		###### Currencies #####
	    # Create Bitcoin EUR
	    $btcEur = new Currency();
		$btcEur->setName("Bitcoin EUR")->setCodeIso("BTCEUR");
		$manager->persist($btcEur);

		#Create Ethereum EUR
	    $ethEur = new Currency();
		$ethEur->setName("Ethereum EUR")->setCodeIso("ETHEUR");
		$manager->persist($ethEur);

		##### Exchanges #####
	    # Create Binance Exchange
	    $binanceExchange = new Exchange();
		$binanceExchange->setLabel("Binance");
		$manager->persist($binanceExchange);

		##### Transactions #####
	    # Create Transactions For userCustomer
	    $transactionUserCustomer = new Transaction();
		$transactionUserCustomer->setAmount(12)
		                        ->setDate($this->faker->dateTime())
		                        ->setIdUser($userCustomer)
		                        ->setType()
		                        ->setValue(12000)
		                        ->setIdCurrency($btcEur)
		                        ->setIdExchange($binanceExchange)
		                        ->setOrderDirection();
		$manager->persist($transactionUserCustomer);

		##### Accounts ######
	    $accountUserCustomer = new Account();
		$accountUserCustomer->setIdUser($userCustomer)
		                    ->setIdExchange($binanceExchange)
		                    ->setPrivateKey($this->faker->md5())
		                    ->setPublicKey($this->faker->md5());
		$manager->persist($accountUserCustomer);

        $manager->flush();
    }
}
