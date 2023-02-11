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
use App\Message\AllTransactionsMessage;
use App\Message\AllTransfertsMessage;
use App\Message\DailyHoldingsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DashboardProvider implements ProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $accountArray   = [];
        $user           = $this->manager->getRepository(User::class)->find($uriVariables['id']);
        if (!$user instanceof User) {
            return ["L'utilisateur n'existe pas"];
        }
        $accounts = $this->manager->getRepository(Account::class)->findBy(['user' => $user]);
        if (empty($accounts)) {
            return ["L'utilisateur n'a pas de compte"];
        }

        foreach ($accounts as $account) {
            if (!$account instanceof Account) {
                continue;
            }
            $exchangeLabel     = $account::EXCHANGE;
            $holdingRepository = $this->manager->getRepository(Holding::class);
            $holdings          = $holdingRepository->findBy(['account' => $account]);

            foreach ($holdings as $holding) {
                if (!$holding instanceof Holding) {
                    continue;
                }
                $accountArray[$exchangeLabel]['holdings'][] = [
                    'currency'                => $holding->getCurrency()?->getCodeIso(),
                    'quantity'                => $holding->getQuantity(),
                    'averagePurchasePrice'    => $holding->getAveragePurchasePrice(),
                    'value'                   => $holding->getQuantity() * $holding->getAveragePurchasePrice(),
                ];
            }
        }

        return [
            'accounts' => $accountArray,
        ];
    }
}
