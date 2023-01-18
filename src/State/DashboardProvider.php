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
use App\Entity\BinanceAccount;
use App\Entity\Currency;
use App\Entity\Holding;
use App\Entity\Transaction;
use App\Entity\User;
use App\Message\FetchBinanceTransactions;
use Doctrine\ORM\EntityManagerInterface;

class DashboardProvider implements ProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $jsonReturn = [
            'total'     => [
                'value'      => 0,
                'percentage' => '0%',
                'gainLoss'   => '+0%',
            ],
            'exchanges' => [],
        ];

        $user = $this->manager->getRepository(User::class)->find($uriVariables['id']);
        if (!$user instanceof User) {
            return ["L'utilisateur n'existe pas"];
        }
        // 1. Get Exchanges
        $accounts                = $this->manager->getRepository(Account::class)->findBy(['user' => $user]);

        // 4. Return performances

        // A. Exchanges
        $exchangesDatasRaw = [];
        foreach ($accounts as $account) {
            $accountPerf         = $this->manager->getRepository(BinanceAccount::class)->getAccountPerf($account, $user);
            $exchangesDatasRaw[] = $accountPerf;
        }
        // B. Total

        $jsonReturn['exchanges'] = $exchangesDatasRaw;

        $account = $this->manager->getRepository(Account::class)->find('1ed91248-b6f0-6b32-8390-c57cccfca4d5');
        // $history = $this->manager->getRepository(BinanceAccount::class)->fetchTransactions($account);

        // $transactions = $this->manager->getRepository(Transaction::class)->getTransactions($user);

        // $this->manager->getRepository(User::class)->findOneBy(["name" => "blabla"]);

        // return $this->manager->getRepository(Currency::class)->findOneBy(["codeIso" => "blblblbl"]);

        // $transactions = $this->bus->dispatch(new FetchBinanceTransactions("1ed91248-b6f0-6b32-8390-c57cccfca4d5"));

        // return array("Transactions en cours de récupération");

        // $this->manager->getRepository(Holding::class)->updateHoldings($user);

        return $jsonReturn;
    }
}
