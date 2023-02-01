<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\Exchange;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ExchangesFixturesProd extends Fixture implements FixtureGroupInterface
{
    /**
     * Class fixtures into dev and prod groups.
     *
     * @return string[]
     */
    public static function getGroups(): array
    {
        return ['prod', 'dev'];
    }

    public function load(ObjectManager $manager): void
    {
        // #### Exchanges #####

        // Create Binance Exchange
        $binanceExchange = new Exchange();
        $binanceExchange->setLabel('binance');
        $manager->persist($binanceExchange);
        $manager->flush();

        $this->addReference('binanceExchange', $binanceExchange);
    }
}
