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
use App\Entity\AccountAssetReturn;
use App\Entity\Asset;
use App\Entity\BinanceAccount;
use App\Entity\Holding;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DashboardProvider implements ProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $manager, private readonly Security $security)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user           = $this->security->getUser();
        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found');
        }
        $accounts = $this->manager->getRepository(Account::class)->findBy(['user' => $user]);
        if (empty($accounts)) {
            return [];
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

            // Get account return
            $accountReturnsAssets = $this->manager->getRepository(AccountAssetReturn::class)->findBy(['account' => $account]);

            // If account exchangeLabel is Binance
            if ('Binance' === $exchangeLabel) {
                $balance = $this->manager->getRepository(BinanceAccount::class)->getAssets($account);
            } else {
                // Get recent holdings for this account for each asset
                $balance      = $this->manager->getRepository(Account::class)->getBalance($account);
            }

            // foreach balance, add 'asset_return' => 0
            foreach ($balance as $key => $value) {
                $assetCode = $value['asset'];
                $assetCode = strtolower($assetCode);
                $assetId   = $this->manager->getRepository(Asset::class)->findOneBy(['code' => $assetCode]);
                if (!$assetId instanceof Asset) {
                    $assetId = '';
                } else {
                    $assetId = $assetId->getId();
                }
                $balance[$key]['asset_id'] = $assetId;
                // check if asset.code == assetCode in accountReturnsAssets
                $balance[$key]['asset_return'] = 0;
                foreach ($accountReturnsAssets as $accountReturnsAsset) {
                    if (!$accountReturnsAsset instanceof AccountAssetReturn) {
                        continue;
                    }
                    $assetReturnCode = $accountReturnsAsset->getAsset()->getCode();
                    $assetReturnCode = strtolower($assetReturnCode);
                    if ($assetReturnCode === $assetCode) {
                        $return                        = $accountReturnsAsset->getReturnOnInvestment();
                        $return                        = round($return, 2);
                        $balance[$key]['asset_return'] = $return;
                    }
                }
            }

            $returnArray[]  = [
                'exchange'   => $exchangeLabel,
                'account_id' => $account->getId(),
                'name'       => $account->getName(),
                // 'account_return' => $accountReturn,
                'balance'  => $balance,
            ];

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
