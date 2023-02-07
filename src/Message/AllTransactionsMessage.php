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

class AllTransactionsMessage
{
    private Account $account;
    private ?\DateTime $previousUpdate;

    public function __construct(Account $account, ?\DateTime $previousUpdate = null)
    {
        $this->account        = $account;
        $this->previousUpdate = $previousUpdate;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function getPreviousUpdate(): ?\DateTime
    {
        return $this->previousUpdate;
    }
}
