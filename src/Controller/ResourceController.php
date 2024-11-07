<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Resource;
use App\Form\ResourceType;
use Doctrine\ORM\EntityManagerInterface;

class ResourceController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager
    ){
    }

    /**
     * @throws Exception
     */
    #[Route('/resources-create', name: 'resources_create')]
    #[Route('/resources/{id}/edit', name: 'resources_edit')]
    public function new_edit(Request $request, Resource $resource = null): Response
    {
        if ($resource === null) {
            $resource = new Resource();
        } else {
            $resource = $this->entityManager->getRepository(Resource::class)->find(['id' => $resource->getId()]);
            if (!$resource) {
                throw $this->createNotFoundException('resources not found');
            }
        }

        $form = $this->createForm(ResourceType::class, $resource);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$resource->getId()) {
                $this->entityManager->persist($resource);
            }
            $this->entityManager->flush();

            if (!$resource->getId()) {
                return $this->redirectToRoute('resource_show', ['id' => $resource->getId()]);
            }

            return $this->redirectToRoute('resources_list');
        }

        return $this->render('form/new_edit.html.twig', [
            'form' => $form->createView(),
            'objectForm' => $resource,
            'title' => 'resources'
        ]);
    }

    #[Route('/resources', name: 'resources_list')]
    public function lists(): Response
    {
       $resources = $this->entityManager->getRepository(Resource::class)->findAll();
        $datas = array_map(fn(Resource $resource) => [
            'id' => $resource->getId(),
            'type' => $resource->getType()?->toArray()['nom'],
            'montant_total' => $resource->getMontantTotal(),
            'montant_restant' => $resource->getMontantRestant(),
            'mensualite' => $resource->getMensualite(),
            'taux' => $resource->getTaux(),
            'reserve' => $resource->getReserve(),
            'date_debut' => $resource->getDateDebut()?->format('Y-m-d H:i:s'),
            'date_fin' => $resource->getDateFin()?->format('Y-m-d H:i:s'),
            'date_prelevement' => $resource->getDatePrelevement()?->format('Y-m-d H:i:s'),
            'operation' => $resource->getOperation()?->getNom() ?? '',
            'organisme' => $resource->getOrganisme()?->getNom() ?? '',
            ], $resources);
      // dd( $resources[0]->getCustomers()->toArray()[0]->getNom());
      // dd($datas);
        return $this->render('lists.html.twig', [
            'datas' =>  $datas,
            'title' => 'resources'
        ]);
    }

    #[Route('/resources/{id}', name: 'resources_show')]
    public function show(Resource $resource): Response
    {
        $resource = $this->entityManager->getRepository(Resource::class)->findOneBy(['id' => $resource->getId()]);
        return $this->render('resources/show.html.twig', [
            'resources' => $resource
        ]);
    }

    #[Route('/resources/{id}/delete', name: 'resources_delete')]
    public function delete(Resource $resource): RedirectResponse
    {
        $this->entityManager->remove($resource);
        $this->entityManager->flush();

        return $this->redirectToRoute('resource_list');
    }
}