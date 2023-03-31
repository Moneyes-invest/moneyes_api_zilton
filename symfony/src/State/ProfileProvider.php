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
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ProfileProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        // #[Autowire('@api_platform.doctrine.orm.state.item_provider')]
        // private readonly ProviderInterface $ormProvider
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): User|array
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return ["Vous n'êtes pas connecté"];
        }
        $uriVariables['id'] = $user->getId()->toRfc4122();

        // /** @var User $provide */
        // $provide = $this->ormProvider->provide($operation, $uriVariables, $context);

        return $user;
    }
}
