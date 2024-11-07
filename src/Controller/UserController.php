<?php

namespace App\Controller;

use App\Entity\Civility;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\ImageUploadService;
use App\Traits\ImageUploaderTrait;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    use ImageUploaderTrait;

    public function __construct(
        public EntityManagerInterface $entityManager,
        public ImageUploadService     $imageUploadService,
        public UserPasswordHasherInterface $passwordHasher,
    ){
    }

    /**
     * @throws Exception
     */
    #[Route('/users/create', name: 'user_create')]
    #[Route('/users/{id}/edit', name: 'user_edit')]
    public function new_edit(Request $request, User $user = null): Response
    {
        $civilities = $this->entityManager->getRepository(Civility::class)->findAll();
        $civilityChoices = [];
        foreach ($civilities as $civility) {
            $civilityChoices[$civility->getId()] = $civility->getCode();
        }

        if (!$user instanceOf User) {
            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user, [
                'civilities' =>  $civilityChoices
            ]);
        } else {
            $user = $this->entityManager->getRepository(User::class)->find(['id' => $user->getId()]);
            if (!$user) {
                throw $this->createNotFoundException('user not found');
            }
            $form = $this->createForm(UserType::class, $user, [
                'civilities' =>  $civilityChoices
            ]);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $imagePath = $this->imageUploadService->upload($photoFile);
                $user->setPhoto($imagePath);
            }

            if (!$user->getId()) {
                $this->entityManager->persist($user);
            }

            if($form->has('plainPassword')) {
                $plainPassword = $form->get('plainPassword')->getData();
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }


            $this->entityManager->flush();

            if (!$user->getId()) {
                return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
            }

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/new_edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/users', name: 'user_list')]
    public function lists(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        return $this->render('lists.html.twig', [
            'datas' => array_map(fn(User $user) => $user->toArray(), $users),
            'title' => 'user'
        ]);
    }

    #[Route('/users/{id}', name: 'user_show')]
    public function show(User $user): Response
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $user->getId()]);
        $totals = [];
        if ($user->getOrders()->count() > 0) {
            foreach ($user->getOrders() as $order) {
                if ($order->getCurrency() !== null) {
                    $totals[$order->getCurrency()] = isset($totals[$order->getCurrency()])
                        ? $totals[$order->getCurrency()] + $order->getPrice()
                        : $order->getPrice();
                }
            }
        }
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'civilities' => $this->entityManager->getRepository(Civility::class)->findAll(),
            'totals' => $totals
        ]);
    }

    #[Route('/users/{id}/delete', name: 'user_delete')]
    public function delete(User $user): RedirectResponse
    {
        foreach ($user->getModifiedFiles() as $file) {
            $file->setLastModifiedBy(null);
        }
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('user_list');
    }
}