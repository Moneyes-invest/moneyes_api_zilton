<?php

namespace App\Entity;

use App\Repository\BinanceAccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BinanceAccountRepository::class)]
class BinanceAccount extends Account
{
}
