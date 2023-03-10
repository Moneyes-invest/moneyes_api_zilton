<?php

declare(strict_types=1);

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
use App\Entity\Holding;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AccountDetailProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly Security               $security,
        #[Autowire('@api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface      $ormProvider
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?array
    {
        $jsonReturn = [
            'holdings' => [],
            'accountLabel' => '',
        ];
        $user = $this->security->getUser();
        /** @var Account $account */
        $account = $this->ormProvider->provide($operation, $uriVariables, $context);
        if (!$account instanceof Account) {
            return null;
        }
        if ($account->getUser() !== $user && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new \Exception('You are not allowed to access this account');
        }
        $jsonReturn['accountLabel'] = 'Binance';
        $holdings = $this->manager->getRepository(Holding::class)->findBy(['account' => $account]);
        foreach ($holdings as $index => $holding) {
            $jsonReturn['holdings'][$index] = [
                'quantity' => $holding->getQuantity(),
            ];
        }

        return $jsonReturn;
        // Retrieve the state from somewhere
    }
}
