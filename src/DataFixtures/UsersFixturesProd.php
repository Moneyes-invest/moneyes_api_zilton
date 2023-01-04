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
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class UsersFixturesProd extends Fixture implements FixtureGroupInterface
{
    /**
     * Class fixtures into prod groups.
     *
     * @return string[]
     */
    public static function getGroups(): array
    {
        return ['prod', 'dev'];
    }

    public function load(ObjectManager $manager): void
    {
        // Create admin account
        $userAdmin = new User();
        $userAdmin->setEmail('admin@moneyes.fr')
                  ->setUsername('moneyes')
                  ->setName('Moneyes')
                  ->setLastname('Invest')
                  ->setPassword('$2y$13$nlu.BeAzwBJr0KKcuVcz5.vrFaayO.dsXtvadcgcPfOrXYJ9tJyS6')
                  ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $manager->persist($userAdmin);

        $manager->flush();
    }
}
