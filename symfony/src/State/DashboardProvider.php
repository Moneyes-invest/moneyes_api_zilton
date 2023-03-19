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
use App\Entity\Symbol;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class DashboardProvider implements ProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $manager, private readonly Security $security)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $accountArray   = [];
        $user           = $this->security->getUser();
        if (!$user instanceof User) {
            return ["L'utilisateur n'existe pas"];
        }
        $accounts = $this->manager->getRepository(Account::class)->findBy(['user' => $user]);
        if (empty($accounts)) {
            return ["L'utilisateur n'a pas de compte"];
        }

        $transactions = $this->manager->getRepository(BinanceAccount::class)->fetchTransactions($accounts[0], null, true);

        /*
        return [
            $transactions,
        ];
        */

        $asset = $this->manager->getRepository(Symbol::class)->findAsset("DFSDFS");

        return [
            $asset,
        ];

        /*
        return [
            'accounts' => $accountArray,
        ];
        */
    }
}
