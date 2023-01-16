<?php

namespace App\State;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Account;
use App\Entity\BinanceAccount;
use App\Entity\Currency;
use App\Entity\Exchange;
use App\Entity\Holding;
use App\Entity\Transaction;
use App\Entity\User;
use App\Message\FetchBinanceTransactions;
use App\MessageHandler\FetchBinanceTransactionsHandler;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\MessageBusInterface;
use function PHPUnit\Framework\returnArgument;


class DashboardProvider implements ProviderInterface
{
    /**
     * @param EntityManagerInterface $manager
     * @param MessageBusInterface $bus
     */
    public function __construct(private readonly EntityManagerInterface $manager, private readonly MessageBusInterface $bus)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $jsonReturn = array(
            "total" => array(
                "value" => 0,
                "percentage" => "0%",
                "gainLoss" => "+0%",
            ),
            "exchanges" => array(),
        );

        $user = $this->manager->getRepository(User::class)->find($uriVariables['id']);
        # 1. Get Exchanges
        $exchanges = $this->manager->getRepository(User::class)->getExchanges($user);
        $accounts = $this->manager->getRepository(Account::class)->findBy(["idUser" => $user]);
        $jsonReturn["exchanges"] = $exchanges;

        # 4. Return performances

        # A. Exchanges
        $exchangesDatasRaw = array();
        foreach ($accounts as $account) {
            $accountPerf = $this->manager->getRepository(BinanceAccount::class)->getAccountPerf($account, $user);
            $exchangesDatasRaw[] = $accountPerf;
        }
        # B. Total

        $jsonReturn["exchanges"] = $exchangesDatasRaw;

        $account = $this->manager->getRepository(Account::class)->find("1ed91248-b6f0-6b32-8390-c57cccfca4d5");
        //$history = $this->manager->getRepository(BinanceAccount::class)->fetchTransactions($account);

        //$transactions = $this->manager->getRepository(Transaction::class)->getTransactions($user);

        // $this->manager->getRepository(User::class)->findOneBy(["name" => "blabla"]);

        //return $this->manager->getRepository(Currency::class)->findOneBy(["codeIso" => "blblblbl"]);


        //$transactions = $this->bus->dispatch(new FetchBinanceTransactions("1ed91248-b6f0-6b32-8390-c57cccfca4d5"));

        //return array("Transactions en cours de récupération");

        //$this->manager->getRepository(Holding::class)->updateHoldings($user);

        return array($jsonReturn);

    }
}
