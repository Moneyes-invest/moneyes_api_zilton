<?php

namespace App\State;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Account;
use App\Entity\BinanceAccount;
use App\Entity\Exchange;
use App\Entity\Holding;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;


class DashboardProvider implements ProviderInterface
{
	public function __construct(private readonly EntityManagerInterface $manager) {
	}

	public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
	    $user = $this->manager->getRepository(User::class)->find($uriVariables['id']);

	    # 1. Get Exchanges
	    $exchanges = $this->manager->getRepository(User::class)->getExchanges($user);

	    # 2. Get New Transactions

	    # 3. Update Holdings
	    $this->manager->getRepository(Holding::class)->updateHoldings($user);

	    # 4. Return performances

	    # A. Exchanges
	    $exchangesDatasRaw = array();
		foreach ($exchanges as $exchange){
			$exchangeObject = $this->manager->getRepository(Exchange::class)->find($exchange["idExchange"]);
			$exchangePerf = $this->manager->getRepository(Exchange::class)->getExchangePerf($exchangeObject, $user);
			$exchangesDatasRaw[] = $exchangePerf;
		}
	    # B. Total

	    //tests
	    $account = $this->manager->getRepository(Account::class)->find("1ed91248-b6f0-6b32-8390-c57cccfca4d5");

		$history = $this->manager->getRepository(BinanceAccount::class)->fetchTransactions($account);


		//$transactions = $this->manager->getRepository(Transaction::class)->getTransactions($user);

	    $test = $this->manager->getRepository(BinanceAccount::class)->test($account);

		return $history;

    }
}
