<?php

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;

class UserTest extends ApiTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [
            'json' => [
                'username' => 'admin@moneyes.fr',
                'password' => 'test',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertArrayHasKey('token', $client->getResponse()->toArray());
        $this->assertArrayHasKey('refresh_token', $client->getResponse()->toArray());
    }
}
