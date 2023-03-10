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
use App\Message\NewTransactions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateAccountProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly MessageBusInterface $bus,
        private readonly Security $security,
        #[Autowire('@api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $ormProvider
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Response
    {
        $user = $this->security->getUser();
        /** @var Account $account */
        $account = $this->ormProvider->provide($operation, $uriVariables, $context);
        if (!$account instanceof Account) {
            $jsonResponse = json_encode([
                'User Account Not Found',
            ]);
            if (false === $jsonResponse) {
                throw new \RuntimeException('Unable to encode response');
            }

            return new Response(
                $jsonResponse,
                404,
                ['Content-Type' => 'application/json']);
        }
        if ($account->getUser() !== $user && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new \Exception('You are not allowed to update this account');
        }
        // Get all new transactions
        $this->bus->dispatch(new NewTransactions((string) $account->getId()));
        // Get all new transfers
        $this->manager->getRepository(BinanceAccount::class)->fetchTransfers($account, true);

        $jsonResponse = json_encode([
            'New User Transactions and new Transfers are fetching',
        ]);
        if (false === $jsonResponse) {
            throw new \RuntimeException('Unable to encode response');
        }

        // Return Binance details endpoint
        return new Response(
            $jsonResponse,
            204,
            ['Content-Type' => 'application/json']);
    }
}
