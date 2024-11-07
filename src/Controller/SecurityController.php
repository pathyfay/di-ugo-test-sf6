<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @param Security $security
     * @param JWTTokenManagerInterface $jwtManagerInterface
     * @return JsonResponse
     */
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(
        Security $security,
        JWTTokenManagerInterface    $jwtManagerInterface
    ): JsonResponse {
        $user = $security->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'accessToken' => $jwtManagerInterface->create($user),
            'user' => [
                'id' => $user->getId(),
                'uuid' => $user->getUuid(),
                'firstName' => $user->getFirstName(),
                'email' => $user->getEmail(),
                'role' => $user->getRoles(),
                'lastName' => $user->getLastName(),
                'matriculate' => $user->getMatriculate(),
                'dateOfBirth' => $user->getDateOfBirth()?->format('Y-m-d'),
                'civility' => $user->getCivility()?->getCode(),
                'phone' => $user->getPhone(),
                'mobile' => $user->getMobile(),
                'city' => $user->getCity(),
                'postalCode' => $user->getPostalCode(),
                'photo' => $user->getPhoto(),
                'streetAddress' => $user->getStreetAddress()
            ]
        ], Response::HTTP_OK);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
