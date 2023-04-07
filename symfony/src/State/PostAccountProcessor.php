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
use ApiPlatform\State\ProcessorInterface;
use App\Binance\API;
use App\Entity\Account;
use Hyperf\HttpMessage\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostAccountProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface $processor
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof Account) {
            throw new \InvalidArgumentException('Invalid data');
        }
        $publicKey  = $data->getPublicKey();
        $privateKey = $data->getPrivateKey();
        $client     = new API($publicKey, $privateKey);
        if (null !== $data->getExchange() && 'Binance' === $data->getExchange()->getLabel()) {
            try {
                $client->accountStatus();
            } catch (\Exception $e) {
                throw new NotFoundHttpException('Invalid credentials :'.$e->getMessage());
            }
            if (!$client->checkApiKeys()) {
                throw new NotAcceptableHttpException('Invalid restrictions');
            }
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
