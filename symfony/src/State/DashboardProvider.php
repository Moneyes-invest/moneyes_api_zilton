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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class DashboardProvider implements ProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $manager, private readonly Security $security)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user           = $this->security->getUser();
        if (!$user instanceof User) {
            return ["L'utilisateur n'existe pas"];
        }
        $accounts = $this->manager->getRepository(Account::class)->findBy(['user' => $user]);
        if (empty($accounts)) {
            return ["L'utilisateur n'a pas de compte"];
        }

        $returnArray    = [];
        foreach ($accounts as $account) {
            if (!$account instanceof Account) {
                continue;
            }
            // Check if account have exchange
            if (!$account->getExchange()) {
                $exchangeLabel = 'Unknown';
            } else {
                $exchangeLabel = $account->getExchange()->getLabel();
            }

            // If account exchangeLabel is Binance
            if ('Binance' === $exchangeLabel) {
                $balanceBinance = $this->manager->getRepository(BinanceAccount::class)->getAssets($account);
                $returnArray[]  = [
                    'exchange' => $exchangeLabel,
                    'balance'  => $balanceBinance,
                ];
            } else {
                // Get recent holdings for this account for each asset
                $holdings      = $this->manager->getRepository(Account::class)->getBalance($account);
                $returnArray[] = [
                    'exchange' => $exchangeLabel,
                    'balance'  => $holdings,
                ];
            }

            /*
            $holdingRepository = $this->manager->getRepository(Holding::class);
            $holdings          = $holdingRepository->findBy(['account' => $account]);

            foreach ($holdings as $holding) {
                if (!$holding instanceof Holding) {
                    continue;
                }
                $accountArray[$exchangeLabel]['holdings'][] = [
                    'currency'                => $holding->getAsset()?->getCode(),
                    'quantity'                => $holding->getQuantity(),
                ];
            }
            */
        }

        return $returnArray;
        /*
        return [
            'accounts' => $accountArray,
        ];
        */
    }
}
