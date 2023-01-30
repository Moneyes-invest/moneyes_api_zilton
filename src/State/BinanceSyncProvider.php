<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Account;
use App\Entity\BinanceAccount;
use App\Message\FetchBinanceTransactions;
use App\Repository\BinanceAccountRepository;
use Binance\API;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class BinanceSyncProvider implements ProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $manager, private readonly MessageBusInterface $bus)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $idAccount = $uriVariables['id']; //user id
        $account = $this->manager->getRepository(Account::class)->find($idAccount); //user account
        $binanceApi = new API($account->getPublicKey(), $account->getPrivateKey()); //Binance api customerBinanceApi
        $holdings = $this->manager->getRepository(BinanceAccount::class)->getAssets($account); //user holdings assets

        //Get all transactions for each symbol by messenger handler
        $this->bus->dispatch(new FetchBinanceTransactions((string)$account->getId()));

        //Return Binance details endpoint
        return $holdings;
    }
}
