<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\Symbol;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class SymbolsFixtures extends Fixture implements FixtureGroupInterface
{
    public const SYMBOLS = [
        'BTCUSDT',
        'ETHUSDT',
        'BNBUSDT',
        'ADAUSDT',
        'XRPUSDT',
        'DOTUSDT',
        'LTCUSDT',
        'LINKUSDT',
        'BCHUSDT',
        'XLMUSDT',
        'UNIUSDT',
        'THETAUSDT',
        'TRXUSDT',
        'VETUSDT',
        'DOGEUSDT',
        'SOLUSDT',
        'ETCUSDT',
        'FILUSDT',
        'XMRUSDT',
        'EOSUSDT',
        'AAVEUSDT',
    ];

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
        foreach (self::SYMBOLS as $symbolString) {
            $symbol = new Symbol();
            $symbol->setCode($symbolString);
            $manager->persist($symbol);
            $this->addReference('symbol'.$symbolString, $symbol);
        }
        $manager->flush();
    }
}
