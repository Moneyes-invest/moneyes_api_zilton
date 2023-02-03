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
use App\Entity\BinanceAccount;
use App\Entity\Currency;
use App\Entity\Holding;
use App\Entity\Transaction;
use App\Entity\User;
use App\Message\BinanceOwnedTransactionsMessage;
use Doctrine\ORM\EntityManagerInterface;
use WebSocket\Client;

class DashboardProvider implements ProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $jsonReturn = [
            'total' => [
                'value' => 0,
                'percentage' => '0%',
                'gainLoss' => '+0%',
            ],
            'exchanges' => [],
        ];

        $user = $this->manager->getRepository(User::class)->find($uriVariables['id']);
        if (!$user instanceof User) {
            return ["L'utilisateur n'existe pas"];
        }
        // 1. Get Exchanges
        $accounts = $this->manager->getRepository(Account::class)->findBy(['user' => $user]);

        // 4. Return performances

        // A. Exchanges
        $exchangesDatasRaw = [];
        foreach ($accounts as $account) {
            $accountPerf = $this->manager->getRepository(BinanceAccount::class)->getAccountPerf($account, $user);
            $exchangesDatasRaw[] = $accountPerf;
        }
        // B. Total

        $jsonReturn['exchanges'] = $exchangesDatasRaw;



        $account = $this->manager->getRepository(Account::class)->find('1ed97700-eb6d-626c-9a60-fb47ac0556d3');

        $history = $this->manager->getRepository(BinanceAccount::class)->fetchTransactions($account);

        return $history;

        // $transactions = $this->manager->getRepository(Transaction::class)->getTransactions($user);

        // $this->manager->getRepository(User::class)->findOneBy(["name" => "blabla"]);

        // return $this->manager->getRepository(Currency::class)->findOneBy(["codeIso" => "blblblbl"]);

        // $transactions = $this->bus->dispatch(new BinanceOwnedTransactionsMessage("1ed91248-b6f0-6b32-8390-c57cccfca4d5"));

        // return array("Transactions en cours de récupération");

        // $this->manager->getRepository(Holding::class)->updateHoldings($user);


        $timestamp = round(microtime(true) * 1000);


        $query = "apiKey=pXihR7QxSJtRuPwNJ3c3z9fFIlfrXPS4j16mBRKnby8EtLkPvR8DJ3fQTtZ2ggjg&symbol=BTCUSDT&timestamp={$timestamp}";

        $signature = hash_hmac('sha256', $query, 'Qcc8KLybfKZ22UB70PQVQqWzYnEO4l0RCmFqPzrg46Uwt3bwyOgGUdF0JfnJwSnd');


        $message = '{"id": "f4ce6a53-a29d-4f70-823b-4ab59391d6e8","method": "myTrades","params": {"symbol": "ETHUSD ","apiKey": "pXihR7QxSJtRuPwNJ3c3z9fFIlfrXPS4j16mBRKnby8EtLkPvR8DJ3fQTtZ2ggjg","signature": "' . $signature . '","timestamp": ' . $timestamp . '}}';

        $client = new Client("wss://ws-api.binance.com:443/ws-api/v3");
        $client->text($message);
        $return = $client->receive();
        $client->close();

        $json = json_decode($return, true);

        dd($json);

        return $return;

        //return $jsonReturn;
    }
}
