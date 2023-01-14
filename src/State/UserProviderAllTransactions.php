<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Account;
use App\Entity\BinanceAccount;
use App\Entity\Holding;
use App\Entity\User;
use App\Message\FetchBinanceTransactions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class UserProviderAllTransactions implements ProviderInterface
{


    /**
     * @param EntityManagerInterface $manager
     * @param MessageBusInterface $bus
     */
    public function __construct(private readonly EntityManagerInterface $manager, private readonly MessageBusInterface $bus)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        # Get Variables
        $idUser = $uriVariables['id'];
        $user = $this->manager->getRepository(User::class)->find($idUser);
        $userAccounts = $this->manager->getRepository(Account::class)->findBy(["idUser" => $idUser]);

        # 1. Flush User Holdings
        $this->manager->getRepository(User::class)->flushHoldings($user);


        # 2. Fetch all transactions for all accounts
        foreach ($userAccounts as $userAccount){
            # 1. Delete all Transactions about this User Account
            $this->manager->getRepository(Account::class)->flushTransactions($userAccount);
            # 2. Fetch all Account Transactions
            $this->bus->dispatch(new FetchBinanceTransactions($userAccount->getId()));
        }

        return array(
            "Transactions are fetching"
        );

    }
}
