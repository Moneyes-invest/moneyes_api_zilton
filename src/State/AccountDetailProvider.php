<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Account;
use App\Entity\Holding;
use Doctrine\ORM\EntityManagerInterface;

class AccountDetailProvider implements ProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $jsonReturn = [
            'holdings'     => [],
            'accountLabel'     => "",
        ];

        $account = $this->manager->getRepository(Account::class)->find($uriVariables['id']);
        if (!$account instanceof Account) {
            return ["Le compte n'existe pas"];
        }
        $jsonReturn["accountLabel"] = "Binance";
        $holdings                = $this->manager->getRepository(Holding::class)->findBy(['account' => $account]);
        foreach ($holdings as $index => $holding) {
            $jsonReturn["holdings"][$index] = array(
                "currency" => $holding->getCurrency(),
                "quantity" => $holding->getQuantity(),
                "averagePurchasePrice" => $holding->getAveragePurchasePrice(),
            );
        }
        return $jsonReturn;
        // Retrieve the state from somewhere
    }
}
