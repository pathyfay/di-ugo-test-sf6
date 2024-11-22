<?php

namespace App\Controller;

use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\RedisAdapter;
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

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/test-session', name: 'test_session')]
    public function testSession(): Response
    {
        $redis = new RedisAdapter(RedisAdapter::createConnection('redis://redis:6379'));
        $cacheItem = $redis->getItem('my_cache_key');
        $cacheItem->set('my_value');
        $redis->save($cacheItem);

        $value = $redis->getItem('my_cache_key')->get();

        return new Response("La valeur de la session est : $value");
    }
}
