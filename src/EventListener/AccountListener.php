<?php

namespace App\EventListener;

use App\Entity\Account;
use App\Entity\Exchange;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Account::class)]
class AccountListener
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function postUpdate(Account $account, LifecycleEventArgs $event): void
    {
        if (null === $account::EXCHANGE) {
            throw new \Exception('Do not implement Account class use child class instead');
        }
        $exchangeRepository = $this->entityManager->getRepository(Exchange::class);
        $exchange           = $exchangeRepository->findOneBy(['name' => $account::EXCHANGE]);
        if (null === $exchange) {
            throw new \Exception('Exchange not found');
        }
        $account->setExchange($exchange);
    }
}
