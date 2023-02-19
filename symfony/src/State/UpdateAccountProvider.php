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
use App\Message\NewTransactions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateAccountProvider implements ProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $manager, private readonly MessageBusInterface $bus)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Response
    {
        // Retrieve the state from somewhere

        $idAccount = $uriVariables['id']; // user id
        $account   = $this->manager->getRepository(Account::class)->find($idAccount); // user account

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
        $this->bus->dispatch(new NewTransactions((string) $account->getId()));

        $jsonResponse = json_encode([
            'New User Transactions are fetching',
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
