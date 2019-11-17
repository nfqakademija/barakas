<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class PublishController extends AbstractController
{
    /**
     * @Route("/publish", name="publish")
     */
    public function index()
    {
        return $this->render('publish/index.html.twig', [
            'controller_name' => 'PublishController',
        ]);
    }

    public function __invoke(Publisher $publisher): Response
    {
        $update = new Update(
            'http://127.0.0.1:9090/demo/books/1.jsonld',
            json_encode(['status' => 'OutOfStock'])
        );

        // The Publisher service is an invokable object
        $publisher($update);

        return new Response('published!');
    }
}
