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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SyncStatusProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        #[Autowire('@api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $ormProvider
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Account|array
    {
        /** @var Account $account */
        $account = $this->ormProvider->provide($operation, $uriVariables, $context);
        if (!$account instanceof Account) {
            throw new NotFoundHttpException('Account not found');
        }
        $account->checkSynchroStatus();
        $this->entityManager->flush();

        return $account;
    }
}
