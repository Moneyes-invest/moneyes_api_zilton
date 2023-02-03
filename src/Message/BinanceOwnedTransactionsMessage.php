<?php

declare(strict_types=1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Message;

use App\Entity\Account;
use App\Entity\Currency;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;

class BinanceOwnedTransactionsMessage
{
    private string $accountId;
    private ?\DateTime $previousUpdate;
    private array $symbolsBalance;

    public function __construct(string $accountId, ?\DateTime $previousUpdate = null)
    {
        $this->accountId = $accountId;
        $this->previousUpdate = $previousUpdate;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function getPreviousUpdate(): ?\DateTime
    {
        return $this->previousUpdate;
    }


}