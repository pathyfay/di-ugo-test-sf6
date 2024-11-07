<?php

namespace App\Controller;

use App\Entity\Operation;
use App\Form\OperationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OperationController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    #[Route('/operations', name: 'operation_list')]
    public function list(): Response
    {
        $operations = $this->entityManager->getRepository(Operation::class)->findAll();
        return $this->render('lists.html.twig', [
            'datas' => array_map(fn(Operation $operations) => $operations->toArray(), $operations),
            'title' => 'operation'
        ]);
    }

    #[Route('/operations-create', name: 'operation_create')]
    #[Route('/operations/{id}/edit', name: 'operation_edit')]
    public function new_edit(Request $request, operation $operation = null): Response
    {
        if ($operation === null) {
            $operation = new Operation();
        } else {
            $operation = $this->entityManager->getRepository(Operation::class)->find(['id' => $operation->getId()]);
            if (!$operation) {
                throw $this->createNotFoundException('operation not found');
            }
        }

        $form = $this->createForm(OperationType::class, $operation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$operation->getId()) {
                $this->entityManager->persist($operation);
            }
            $this->entityManager->flush();

            return $this->redirectToRoute('operation_list');
        }

        return $this->render('form/new_edit.html.twig', [
            'form' => $form->createView(),
            'objectForm' => $operation,
            'title' => 'operation'
        ]);
    }

    #[Route('/operation/{id}', name: 'operation_show')]
    public function show(Operation $operation): Response
    {
        $operation = $this->entityManager->getRepository(Operation::class)->findOneBy(['id' => $operation->getId()]);
        return $this->render('operation/show.html.twig', [
            'operation' => $operation
        ]);
    }

    #[Route('/operations/{id}/delete', name: 'operation_delete')]
    public function delete(Operation $operation): RedirectResponse
    {
        $this->entityManager->remove($operation);
        $this->entityManager->flush();

        return $this->redirectToRoute('operation_list');
    }
}
