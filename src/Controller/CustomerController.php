<?php

namespace App\Controller;

use App\Service\ImageUploadService;
use App\Traits\ImageUploaderTrait;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Customer;
use App\Form\CustomerType;
use Doctrine\ORM\EntityManagerInterface;

class CustomerController extends AbstractController
{
    use ImageUploaderTrait;
    public function __construct(public EntityManagerInterface $entityManager, public ImageUploadService $imageUploadService)
    {
    }

    /**
     * @throws Exception
     */
    #[Route('/customer/new', name: 'customer_new')]
    #[Route('/customer/{id}/edit', name: 'customer_edit')]
    public function new_edit(Request $request, Customer $customer = null): Response
    {
        if ($customer === null) {
            $customer = new Customer();
        } else {
            $customer = $this->entityManager->getRepository(Customer::class)->find(['id' => $customer->getId()]);
            if (!$customer) {
                throw $this->createNotFoundException('Customer not found');
            }
        }

        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $imagePath = $this->imageUploadService->upload($photoFile);
                $customer->setPhoto($imagePath);
            }

            if (!$customer->getId()) {
                $this->entityManager->persist($customer);
            }
            $this->entityManager->flush();

            if (!$customer->getId()) {
                return $this->redirectToRoute('customer_show', ['id' => $customer->getCustomerId()]);
            }

            return $this->redirectToRoute('customer_lists');
        }

        return $this->render('customer/new_edit.html.twig', [
            'form' => $form->createView(),
            'customer' => $customer
        ]);
    }

    #[Route('/customer/lists', name: 'customer_lists')]
    public function lists(): Response
    {
        $customers = $this->entityManager->getRepository(Customer::class)->findAll();
        return $this->render('customer/lists.html.twig', [
            'customers' => $customers,
        ]);
    }

    #[Route('/customer/{id}', name: 'customer_show')]
    public function show(Customer $customer): Response
    {
        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['id' => $customer->getId()]);
        $totals = [];
        if ($customer->getOrders()->count() > 0) {
            foreach ($customer->getOrders() as $order) {
                if ($order->getCurrency() !== null) {
                    $totals[$order->getCurrency()] = isset($totals[$order->getCurrency()])
                        ? $totals[$order->getCurrency()] + $order->getPrice()
                        : $order->getPrice();
                }
            }
        }

        return $this->render('customer/show.html.twig', [
            'customer' => $customer,
            'totals' => $totals
        ]);
    }

    #[Route('/customer/{id}/delete', name: 'customer_delete')]
    public function delete(Customer $customer): RedirectResponse
    {
        $this->entityManager->remove($customer);
        $this->entityManager->flush();

        return $this->redirectToRoute('customer_lists');
    }
}