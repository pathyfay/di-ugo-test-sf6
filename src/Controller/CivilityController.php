<?php

namespace App\Controller;

use App\Entity\Civility;
use App\Entity\User;
use App\Form\CivilityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CivilityController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    #[Route('/civilities', name: 'civility_list')]
    public function list(): Response
    {
        $civilities = $this->entityManager->getRepository(Civility::class)->findAll();
        return $this->render('lists.html.twig', [
            'datas' => array_map(fn(Civility $civility) => $civility->toArray(), $civilities),
            'title' => 'civility'
        ]);
    }

    #[Route('/civility-create', name: 'civility_create')]
    #[Route('/civilities/{id}/edit', name: 'civility_edit')]
    public function new_edit(Request $request, Civility $civility = null): Response
    {
        if ($civility === null) {
            $civility = new Civility();
        } else {
            $civility = $this->entityManager->getRepository(Civility::class)->find(['id' => $civility->getId()]);
            if (!$civility) {
                throw $this->createNotFoundException('civility not found');
            }
        }

        $form = $this->createForm(CivilityType::class, $civility);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$civility->getId()) {
                $this->entityManager->persist($civility);
            }
            $this->entityManager->flush();

            return $this->redirectToRoute('civility_list');
        }

        return $this->render('form/new_edit.html.twig', [
            'form' => $form->createView(),
            'objectForm' => $civility,
            'title' => 'civility'
        ]);
    }

    #[Route('/civilities/{id}/delete', name: 'civility_delete')]
    public function delete(Civility $civility): RedirectResponse
    {
        $this->entityManager->remove($civility);
        $this->entityManager->flush();

        return $this->redirectToRoute('civility_list');
    }
}
