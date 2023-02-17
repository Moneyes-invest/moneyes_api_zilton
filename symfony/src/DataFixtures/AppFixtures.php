<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    /**
     * Init Fixture Class. Load Faker.
     */
    public function __construct()
    {
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
    }

    public function getDependencies(): array
    {
        return [
            ExchangesFixturesProd::class,
            SymbolsFixtures::class,
            UsersFixturesProd::class,
        ];
    }
}
