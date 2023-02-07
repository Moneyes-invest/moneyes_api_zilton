<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventListener;

use App\Entity\Account;
use App\Entity\Exchange;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Account::class)]
class AccountListener
{
    public function prePersist(Account $account, LifecycleEventArgs $args): void
    {
        $exchange           = $account::EXCHANGE;
        $exchangeRepository = $args->getObjectManager()->getRepository(Exchange::class);
        $exchange           = $exchangeRepository->findOneBy(['label' => $exchange]);
        if (!$exchange) {
            $exchange = new Exchange();
            $exchange->setLabel($account::EXCHANGE);
        }
        $account->setExchange($exchange);
    }
}
