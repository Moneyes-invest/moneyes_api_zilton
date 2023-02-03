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
use App\Entity\User;
use App\Message\BinanceOwnedTransactionsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class UserProviderAllTransactions implements ProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $manager, private readonly MessageBusInterface $bus)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        // Get Variables
        $idUser       = $uriVariables['id'];
        $user         = $this->manager->getRepository(User::class)->find($idUser);
        $userAccounts = $this->manager->getRepository(Account::class)->findBy(['user' => $idUser]);

        if (empty($user) || empty($userAccounts)) {
            return ["L'utilisateur n'existe pas"];
        }

        // 1. Flush User Holdings
        $this->manager->getRepository(User::class)->flushHoldings($user);


        //get user balances
        //$binanceApi = $this->manager->getRepository(BinanceAccount::class)->customerBinanceApi($user);
        //$balances

        // 2. Fetch all transactions for all accounts
        foreach ($userAccounts as $userAccount) {
            if ($userAccount instanceof Account) {
                // 1. Delete all Transactions about this User Account
                $this->manager->getRepository(Account::class)->flushTransactions($userAccount);
                // 2. Fetch all Account Transactions
                $this->bus->dispatch(new BinanceOwnedTransactionsMessage((string) $userAccount->getId()));
            }
        }

        return [
            'Transactions are fetching',
        ];
    }
}
