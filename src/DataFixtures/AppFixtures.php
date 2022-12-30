<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

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
        // Create customer
        $userCustomer = new User();
        $userCustomer->setEmail($this->faker->email())
                      ->setUsername($this->faker->userName())
                      ->setName($this->faker->name())
                      ->setRoles(['ROLE_USER'])
                      ->setLastname($this->faker->lastName())
                      ->setPassword('$2y$13$Avfr0GAnTYFWtBdm7lOi3eiZK0.frdZ4hjV2aBAu7gfdg2QFLy.EK');
        $manager->persist($userCustomer);

        // Create admin account
        $userAdmin = new User();
        $userAdmin->setEmail('admin@moneyes.fr')
                   ->setUsername('moneyes')
                   ->setName('Moneyes')
                   ->setLastname('Invest')
                   ->setPassword('$2y$13$Q7K9ZktfHj9n.QelyHjauuZkeneJlCUJvVcPPbq3GsdPYYcarY53G')
                   ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $manager->persist($userAdmin);

        $manager->flush();
    }
}
