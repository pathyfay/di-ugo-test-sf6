<?php

namespace App\Controller;

use App\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CustomerOrdersController extends AbstractController
{
    public function __invoke(Customer $data): JsonResponse
    {
        return $this->json($data->getOrders());
    }
}
