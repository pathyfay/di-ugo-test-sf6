<?php

namespace App\Controller;

use App\Entity\Organisme;
use App\Form\OrganismeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrganisationController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager
    ){
    }

    #[Route('/organismes', name: 'organisme_list')]
    public function list(): Response
    {
        $organismes = $this->entityManager->getRepository(Organisme::class)->findAll();
        return $this->render('lists.html.twig', [
            'datas' => array_map(fn(Organisme $organisme) => $organisme->toArray(), $organismes),
            'title' => 'organisme'
        ]);
    }

    #[Route('/organismes-create', name: 'organisme_create')]
    #[Route('/organismes/{id}/edit', name: 'organisme_edit')]
    public function new_edit(Request $request, Organisme $organisme = null): Response
    {
        if ($organisme === null) {
            $organisme = new Organisme();
        } else {
            $organisme = $this->entityManager->getRepository(Organisme::class)->find(['id' => $organisme->getId()]);
            if (!$organisme) {
                throw $this->createNotFoundException('organisme not found');
            }
        }

        $form = $this->createForm(OrganismeType::class, $organisme);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$organisme->getId()) {
                $this->entityManager->persist($organisme);
            }
            $this->entityManager->flush();

            return $this->redirectToRoute('organisme_list');
        }

        return $this->render('form/new_edit.html.twig', [
            'form' => $form->createView(),
            'objectForm' => $organisme,
            'title' => 'organisme'
        ]);
    }

    #[Route('/organisme/{id}', name: 'organisme_show')]
    public function show(Organisme $organisme): Response
    {
        $organisme = $this->entityManager->getRepository(Organisme::class)->findOneBy(['id' => $organisme->getId()]);
        return $this->render('organisme/show.html.twig', [
            'organisme' => $organisme
        ]);
    }

    #[Route('/organismes/{id}/delete', name: 'organisme_delete')]
    public function delete(Organisme $organisme): RedirectResponse
    {
        $this->entityManager->remove($organisme);
        $this->entityManager->flush();

        return $this->redirectToRoute('organisme_list');
    }
}
