<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    #[Route('/orders', name: 'order_lists')]
    public function list(): Response
    {
        $orders = $this->entityManager->getRepository(Order::class)->findAll();
        return $this->render('order/lists.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/order/new', name: 'order_new')]
    #[Route('/order/{id}/edit', name: 'order_edit')]
    public function new_edit(Request $request, Order $order = null): Response
    {
        if ($order === null) {
            $order = new Order();
        } else {
            $order = $this->entityManager->getRepository(Order::class)->find(['id' => $order->getId()]);
            if (!$order) {
                throw $this->createNotFoundException('order not found');
            }
        }

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$order->getId()) {
                $this->entityManager->persist($order);
            }
            $this->entityManager->flush();

            return $this->redirectToRoute('order_lists');
        }

        return $this->render('order/new_edit.html.twig', [
            'form' => $form->createView(),
            'order' => $order
        ]);
    }

    #[Route('/order/{id}', name: 'order_show')]
    public function show(Order $order): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['id' => $order->getId()]);
        return $this->render('order/show.html.twig', [
            'order' => $order
        ]);
    }

    #[Route('/order/{id}/delete', name: 'order_delete')]
    public function delete(Order $order): RedirectResponse
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();

        return $this->redirectToRoute('order_lists');
    }
}
