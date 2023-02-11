<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Interface;

use App\Entity\Account;

interface AccountInterface
{
    public function getAssets(Account $account): array;

    public function getAccountSymbols(Account $account): array;

    public function getAllSymbols(Account $account): array;

    public function fetchTransactions(Account $account, ?array $symbolsBalance = null, ?\DateTime $previousUpdate = null): array;
}
