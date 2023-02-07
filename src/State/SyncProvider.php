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
use App\Message\AllTransactionsMessage;
use App\Message\OwnedTransactionsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SyncProvider implements ProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $manager, private readonly MessageBusInterface $bus)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $idAccount = $uriVariables['id']; // user id
        $account   = $this->manager->getRepository(Account::class)->find($idAccount); // user account

        if (!$account instanceof Account) {
            return null;
        }

        $holdings = $this->manager->getRepository($account::class)->getAssets($account); // user holdings assets
        // Get all transactions for each symbol by messenger handler
        $this->bus->dispatch(new OwnedTransactionsMessage($account));
        // Get the rest of transactions (all symbols - user holdings)
        $this->bus->dispatch(new AllTransactionsMessage($account));

        // Return Binance details endpoint
        return $holdings;
    }
}
