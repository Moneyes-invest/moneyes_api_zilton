<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CurrenciesFixturesProd extends Fixture implements FixtureGroupInterface
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
        // ##### Currencies #####
        // Create Bitcoin EUR
        $btcEur = new Currency();
        $btcEur->setName('Bitcoin EUR')->setCodeIso('BTCEUR');
        $manager->persist($btcEur);

        // Create Ethereum EUR
        $ethEur = new Currency();
        $ethEur->setName('Ethereum EUR')->setCodeIso('ETHEUR');
        $manager->persist($ethEur);

        $manager->flush();

        $this->addReference('btcEur', $btcEur);
    }
}
