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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

class SyncProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly MessageBusInterface $bus,
        private readonly Security $security,
        #[Autowire('@api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $ormProvider
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        /** @var Account $account */
        $account = $this->ormProvider->provide($operation, $uriVariables, $context);
        if (!$account instanceof Account) {
            return null;
        }
        if ($account->getUser() !== $user && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new \Exception('You are not allowed to synchronize this account');
        }
        // Check security here
        if (Account::SYNCHRO_IN_PROGRESS === $account->getSynchroStatus()) {
            throw new \Exception('Synchronization already in progress');
        }
        $account->resetSynchro();
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
        return array(
            'account_name' => $account->getName(),
            'holdings'     => $holdings,
        );
    }
}
