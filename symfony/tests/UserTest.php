<?php


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        $client->request(
            method: 'POST',
            uri: '/api/login',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'username' => 'admin@moneyes.fr',
                'password' => 'test',
            ]));

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJson($client->getResponse()->getContent());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $this->assertArrayHasKey('refresh_token', $data);
    }

    public function testLoginFail(): void
    {
        $client = static::createClient();
        $client->request(
            method: 'POST',
            uri: '/api/login',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'username' => ' ',
                'password' => ' ',
            ]));

        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJson($client->getResponse()->getContent());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $data);
    }
}
