<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/test-session', name: 'test_session')]
    public function testSession(): Response
    {
        $this->get('session')->set('key', 'value');
        $value = $this->get('session')->get('key');

        return new Response("La valeur de la session est : $value");
    }
}
