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
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    private Generator $faker;

    /**
     * Init Fixture Class. Load Faker.
     */
    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    /**
     * Class fixtures into dev and prod groups.
     *
     * @return string[]
     */
    public static function getGroups(): array
    {
        return ['dev'];
    }

    public function load(ObjectManager $manager): void
    {
        // #### USERS #####
        // Create customer
        $userCustomer = new User();
        $userCustomer->setEmail($this->faker->email())
                      ->setUsername($this->faker->userName())
                      ->setName($this->faker->name())
                      ->setRoles(['ROLE_USER'])
                      ->setLastname($this->faker->lastName())
                      ->setPassword('$2y$13$Avfr0GAnTYFWtBdm7lOi3eiZK0.frdZ4hjV2aBAu7gfdg2QFLy.EK');
        $manager->persist($userCustomer);

        // Get BTCEUR Reference
        /** @phpstan-var Currency $btcEur */
        $btcEur = $this->getReference('btceurReference');
        // Get Binance Exchange Reference
        /** @phpstan-var Exchange $binanceExchange */
        $binanceExchange = $this->getReference('binanceExchange');

        // #### Transactions #####
        // Create Transactions For userCustomer
        $transactionUserCustomer = new Transaction();
        $transactionUserCustomer->setQuantity(12)
                                ->setDate($this->faker->dateTime())
                                ->setIdUser($userCustomer)
                                ->setType()
                                ->setPrice(12000)
                                ->setIdCurrency($btcEur)
                                ->setIdExchange($binanceExchange)
                                ->setOrderDirection();
        $manager->persist($transactionUserCustomer);

        // #### Accounts ######
        $accountUserCustomer = new Account();
        $accountUserCustomer->setIdUser($userCustomer)
                            ->setIdExchange($binanceExchange)
                            ->setPrivateKey($this->faker->md5())
                            ->setPublicKey($this->faker->md5());
        $manager->persist($accountUserCustomer);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ExchangesFixturesProd::class,
            CurrenciesFixturesProd::class,
        ];
    }
}
