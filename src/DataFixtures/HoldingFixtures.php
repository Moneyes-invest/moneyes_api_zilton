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
use App\Entity\Holding;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class HoldingFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    /**
     * Class fixtures into prod groups.
     *
     * @return string[]
     */
    public static function getGroups(): array
    {
        return ['dev'];
    }

    public function load(ObjectManager $manager): void
    {
//        $holding = new Holding();
//
//        /** @phpstan-var Account $binanceAccount */
//        $binanceAccount = $this->getReference('binanceAccount');
//        /** @phpstan-var Currency $btcEur */
//        $btcEur = $this->getReference('btcEur');
//
//        $holding->setAccount($binanceAccount)
//                ->setQuantity(0.00000001)->
//
//        $manager->persist($holding);
//        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CurrenciesFixturesProd::class,
            UsersFixturesProd::class,
        ];
    }
}
