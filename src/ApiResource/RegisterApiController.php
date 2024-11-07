<?php
namespace App\ApiResource;

use App\Entity\Civility;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_')]
class RegisterApiController
{
    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $hasher, private ValidatorInterface $validator) {}

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request                     $request,
        EntityManagerInterface      $em,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface    $jwtManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;
        $phone = $data['phone'] ?? null;
        $mobile = $data['mobile'] ?? null;
        $city = $data['city'] ?? null;
        $postalCode = $data['postalCode'] ?? null;
        $photo = $data['photo'] ?? null;
        $address = $data['address'] ?? null;
        $dateOfBirth = isset($data['dateOfBirth']) ? new DateTime($data['dateOfBirth']) : null;
        $roles = $data['roles'] ?? ['ROLE_USER'];
        $matriculate = 'M' . rand(0, 4). '-' . rand(0, 4);

        if (!$firstname || !$lastname || !$email || !$password) {
            return new JsonResponse(['error' => 'email or password or firstname or lastname required'], 400);
        }
        $civility = $em->getRepository(Civility::class)->findOneBy(['nom' => $data['civility'] ?? null]);
        $user = new User();
        $user->setUuid(uniqid());
        $user->setRoles($roles);
        $user->setCivility($civility);
        $user->setPhone($phone);
        $user->setMobile($mobile);
        $user->setCity($city);
        $user->setPostalCode($postalCode);
        $user->setPhoto($photo);
        $user->setStreetAddress($address);
        $user->setDateOfBirth($dateOfBirth);
        $user->setMatriculate($matriculate);
        $user->setFirstName($firstname);
        $user->setLastName($lastname);
        $user->setEmail($email);
        $user->setPassword($passwordHasher->hashPassword($user, $password));

        // validate entity
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $err = [];
            foreach ($errors as $e) {
                $err[] = $e->getPropertyPath().': '.$e->getMessage();
            }
            return new JsonResponse(['errors' => $err], 400);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $this->json(
            [
            'accessToken' => $jwtManager->create($user),
            'user' => [
                'id' => $user->getId(),
                'uuid' => $user->getUuid(),
                'firstName' => $user->getFirstName(),
                'email' => $user->getEmail(),
                'role' => $user->getRoles(),
                'lastName' => $user->getLastName(),
                'matriculate' => $user->getMatriculate(),
                'dateOfBirth' => $user->getDateOfBirth()?->format('Y-m-d'),
                'civility' => $user->getCivility(),
                'phone' => $user->getPhone(),
                'mobile' => $user->getMobile(),
                'city' => $user->getCity(),
                'postalCode' => $user->getPostalCode(),
                'photo' => $user->getPhoto(),
                'streetAddress' => $user->getStreetAddress()
            ]
        ], Response::HTTP_CREATED);
    }
}
