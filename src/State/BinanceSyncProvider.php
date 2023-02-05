<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Account;
use App\Entity\BinanceAccount;
use App\Entity\Holding;
use App\Entity\User;
use App\Message\BinanceAllTransactionsMessage;
use App\Message\BinanceOwnedTransactionsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class BinanceSyncProvider implements ProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $manager, private readonly MessageBusInterface $bus)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $idAccount = $uriVariables['id']; // user id
        $account   = $this->manager->getRepository(Account::class)->find($idAccount); // user account

        if (!$account instanceof BinanceAccount) {
            return null;
        }

        $holdings = $this->manager->getRepository(BinanceAccount::class)->getAssets($account); // user holdings assets

        // Get all transactions for each symbol by messenger handler
        $this->bus->dispatch(new BinanceOwnedTransactionsMessage((string) $account->getId()));

        // Get the rest of transactions (all symbols - user holdings)
        $this->bus->dispatch(new BinanceAllTransactionsMessage((string)$account->getId()));

        // Return Binance details endpoint
        return $holdings;
    }
}
