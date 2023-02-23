<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class PublishController extends AbstractController
{
    #[Route('/publish', name: 'publish')]
    public function publish(HubInterface $hub): Response
    {
        $update = new Update(
            'https://example.com/my-private-topic',
            json_encode(['status' => 'OutOfStock']),
            true // private
        );

        // Publisher's JWT must contain this topic, a URI template it matches or * in mercure.publish or you'll get a 401
        // Subscriber's JWT must contain this topic, a URI template it matches or * in mercure.subscribe to receive the update
        $hub->publish($update);

        return new Response('private update published!');
    }
}