<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Account;
use App\Entity\BinanceAccount;
use App\Entity\Holding;
use App\Entity\User;
use App\Message\FetchBinanceTransactions;
use App\MessageHandler\FetchBinanceTransactionsOfSymbolsNotOwnedHandler;
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

        //Get the rest of transactions (all symbols - user holdings)
        $this->bus->dispatch(new FetchBinanceTransactionsOfSymbolsNotOwnedHandler((string)$account->getId()));


        //Update holdings
        $user = $account->getUser();
        if ($user instanceof User) {
            $this->manager->getRepository(Holding::class)->updateHoldings($user);
        }

        //Return Binance details endpoint
        return $holdings;
    }
}
