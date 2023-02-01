<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Repository\BinanceAccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BinanceAccountRepository::class)]
class BinanceAccount extends Account
{
    const EXCHANGE = 'binance';
}
