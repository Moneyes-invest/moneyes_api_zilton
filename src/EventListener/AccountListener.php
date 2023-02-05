<?php

namespace App\EventListener;

use App\Entity\Account;
use App\Entity\Exchange;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Account::class)]
class AccountListener
{
    public function prePersist(Account $account, LifecycleEventArgs $args): void
    {
        $exchange = $account::EXCHANGE;
        $exchangeRepository = $args->getObjectManager()->getRepository(Exchange::class);
        $exchange = $exchangeRepository->findOneBy(['label' => $exchange]);
        if (!$exchange) {
            $exchange = new Exchange();
            $exchange->setLabel($account::EXCHANGE);
        }
        $account->setExchange($exchange);
    }
}