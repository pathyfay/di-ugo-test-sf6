<?php

namespace App\Controller;

use App\Entity\Civility;
use App\Entity\CivilStatus;
use App\Form\CivilStatusType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CivilStatusController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    #[Route('/civil-statuses', name: 'civil_status_list')]
    public function list(): Response
    {
        $civilStatuses = $this->entityManager->getRepository(CivilStatus::class)->findAll();
        return $this->render('lists.html.twig', [
            'datas' => array_map(fn(CivilStatus $civilStatus) => $civilStatus->toArray(), $civilStatuses),
            'title' => 'civil-status'
        ]);
    }

    #[Route('/civil-status-create', name: 'civil_status_create')]
    #[Route('/civil-statuses/{id}/edit', name: 'civil_status_edit')]
    public function new_edit(Request $request, Civility $civilStatus = null): Response
    {
        if ($civilStatus === null) {
            $civilStatus = new CivilStatus();
        } else {
            $civilStatus = $this->entityManager->getRepository(CivilStatus::class)->find(['id' => $civilStatus->getId()]);
            if (!$civilStatus) {
                throw $this->createNotFoundException('civil-status not found');
            }
        }

        $form = $this->createForm(CivilStatusType::class, $civilStatus);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$civilStatus->getId()) {
                $this->entityManager->persist($civilStatus);
            }
            $this->entityManager->flush();

            return $this->redirectToRoute('civil_status_list');
        }

        return $this->render('form/new_edit.html.twig', [
            'form' => $form->createView(),
            'objectForm' => $civilStatus,
            'title' => 'civil-status'
        ]);
    }

    #[Route('/civil-statuses/{id}/delete', name: 'civil_status_delete')]
    public function delete(CivilStatus $civilStatus): RedirectResponse
    {
        $this->entityManager->remove($civilStatus);
        $this->entityManager->flush();

        return $this->redirectToRoute('civil_status_list');
    }
}
