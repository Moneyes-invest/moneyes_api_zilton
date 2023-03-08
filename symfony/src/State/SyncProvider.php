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
use App\Message\AllTransactionsMessage;
use App\Message\AllTransfersMessage;
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

        $account->setSynchroStatus(Account::SYNCHRO_IN_PROGRESS);
        $account->setStartedAt(new \DateTime());
        $this->manager->flush();

        $accountRepository = $this->manager->getRepository(BinanceAccount::class);
        $holdings          = $accountRepository->getAssets($account); // user holdings assets
        // Get all transactions for each symbol by messenger handler
        $this->bus->dispatch(new OwnedTransactionsMessage((string) $account->getId()));
        // Get all transfers
        $this->bus->dispatch(new AllTransfersMessage((string) $account->getId()));
        // Get the rest of transactions (all symbols - user holdings)
        $this->bus->dispatch(new AllTransactionsMessage((string) $account->getId()));

        // Return Binance details endpoint
        return $holdings;
    }
}
