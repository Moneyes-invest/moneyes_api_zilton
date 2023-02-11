<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Message;

use App\Entity\Account;

class OwnedTransactionsMessage
{
    private string $accountId;

    public function __construct(string $accountId)
    {
        $this->accountId        = $accountId;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }
}
