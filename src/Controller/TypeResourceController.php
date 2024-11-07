<?php

namespace App\Controller;

use App\Entity\TypeResource;
use App\Form\TypeResourceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TypeResourceController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    #[Route('/type-resources', name: 'type_resource_list')]
    public function list(): Response
    {
        $typeResources = $this->entityManager->getRepository(TypeResource::class)->findAll();
        return $this->render('lists.html.twig', [
            'datas' => array_map(fn(TypeResource $typeResource) => $typeResource->toArray(), $typeResources),
            'title' => 'type-resource'
        ]);
    }

    #[Route('/type-resource-create', name: 'type_resource_create')]
    #[Route('/type-resources/{id}/edit', name: 'type_resource_edit')]
    public function new_edit(Request $request, TypeResource $TypeResource = null): Response
    {
        if ($TypeResource === null) {
            $TypeResource = new TypeResource();
        } else {
            $TypeResource = $this->entityManager->getRepository(TypeResource::class)->find(['id' => $TypeResource->getId()]);
            if (!$TypeResource) {
                throw $this->createNotFoundException('TypeResource not found');
            }
        }

        $form = $this->createForm(TypeResourceType::class, $TypeResource);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$TypeResource->getId()) {
                $this->entityManager->persist($TypeResource);
            }
            $this->entityManager->flush();

            return $this->redirectToRoute('type_resource_list');
        }

        return $this->render('form/new_edit.html.twig', [
            'form' => $form->createView(),
            'objectForm' => $TypeResource,
            'title' => 'type-resource'
        ]);
    }

    #[Route('/type-resources/{id}/delete', name: 'type_resource_delete')]
    public function delete(TypeResource $TypeResource): RedirectResponse
    {
        $this->entityManager->remove($TypeResource);
        $this->entityManager->flush();

        return $this->redirectToRoute('type_resource_list');
    }
}
