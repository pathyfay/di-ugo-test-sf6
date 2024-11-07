<?php

namespace App\ApiResource;

use App\Entity\Civility;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Product;
use App\Service\ImageUploadService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

#[AsController]
#[Route('/api', name: 'api_')]
class UserApiController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public ObjectNormalizer       $objectNormalizer,
        public ImageUploadService     $imageUploadService
    )
    {
    }


//    /**
//     * @return JsonResponse
//     */
//    #[Route('/api/users', name: 'get_users', methods: ['GET'])]
//    public function getusers(): JsonResponse
//    {
//        $users = $this->entityManager->getRepository(User::class)->findAll();
//        if (!$users) {
//            return new JsonResponse(['error' => 'Users not found'], 404);
//        }
//
//        $userDatas = [];
//        foreach ($users as $user) {
//            $userData = $this->getUserNormalize($user);
//            $userDatas[] = $userData;
//        }
//
//        return new JsonResponse($userDatas, 200);
//    }

//    /**
//     * @param int $id
//     * @return JsonResponse
//     */
//    #[Route('/api/users/{id}', name: 'get_user_orders', methods: ['GET'])]
//    public function getUserOrders(int $id): JsonResponse
//    {
//        try {
//            $user = $this->entityManager->getRepository(User::class)->find($id);
//            if (!$user) {
//                return new JsonResponse(['error' => 'User not found'], 404);
//            }
//
//            return new JsonResponse($this->getUserNormalize($user), 200);
//        } catch (Exception $e) {
//            return new JsonResponse(['error' => $e->getMessage()], 400);
//        }
//    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/api/users-new', name: 'create_user', methods: ['POST'])]
    public function createUserOrders(Request $request): JsonResponse
    {
        $user = new User();
        $civility = new Civility();
        $user->setCivility($civility->setCode($request->request->get('title') ?? 'Mr'));
        $user->setLastname($request->request->get('lastname') ?? '');
        $user->setFirstname($request->request->get('firstname') ?? '');
        $user->setPostalCode($request->request->get('postalCode') ?? '');
        $user->setCity($request->request->get('city') ?? '');
        $user->setEmail($request->request->get('email') ?? '');
        $user->setMobile($request->request->get('mobile') ?? '');
        $user->setPhone($request->request->get('phone') ?? '');
        $user->getMatriculate($request->request->get('matriculate') ?? '');
        $user->setStreetAddress($request->request->get('streetAddress') ?? '');
        $user->setDateOfBirth(new DateTime($request->request->get('birthday')) ?? null);
        $photo = $request->files->get('photo');
        if ($photo) {
            $imagePath = $this->imageUploadService->upload($photo);
            $user->setPhoto($imagePath);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(["result" => "OK", "status" => 200, 'message' => 'User created successfully'], 200);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/api/users/{id}/edit', name: 'update_user', methods: ['PUT'])]
    public function updateUserOrders(Request $request, int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $user->setTitle($request->request->get('title') ?? $user->getTitle());
        $user->setLastname($request->request->get('lastname') ?? $user->getLastname());
        $user->setFirstname($request->request->get('firstname') ?? $user->getFirstname());
        $user->setPostalCode($request->request->get('postalCode') ?? $user->getPostalCode());
        $user->setCity($request->request->get('city') ?? $user->getCity());
        $user->setEmail($request->request->get('email') ?? $user->getEmail());
        $user->setMobile($request->request->get('mobile') ?? $user->getMobile());
        $user->setBirthday(new DateTime($request->request->get('dateOfBirth')) ?? $user->getDateOfBirth());
        $user->setPhone($request->request->get('phone') ?? $user->getPhone());
        $user->setMatriculate($request->request->get('matriculate') ?? $user->getMatriculate());
        $user->setStreetAddress($request->request->get('streetAddress') ?? $user->getStreetAddress());
        $user->setDateOfBirth(new DateTime($request->request->get('dateOfBirth')) ?? $user->getDateOfBirth());

        $photo = $request->files->get('photo');
        if ($photo) {
            $imagePath = $this->imageUploadService->upload($photo);
            $user->setPhoto($imagePath);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(["result" => "OK", "status" => 200, 'message' => 'Order updated successfully'], 200);
    }

    /**
     * @param User $user
     * @return array
     */
    private function getUserNormalize(User $user): array
    {
        $ordersData = [];
        $userData = $this->objectNormalizer->normalize($user);
        $userData['dateOfBirth'] = $user->getDateOfBirth()?->format('Y-m-d') ?? '';
        foreach ($user->getOrders() as $order) {
            $ordersData[] = [
                'id' => $order->getId(),
                'user_id' => $order->getUser()->getId(),
                'products' => $order->getProducts()->map(fn(Product $product) => $product->getId())->toArray(),
                'order_date' => $order->getOrderDate()?->format('Y-m-d') ?? '',
                'quantity' => $order->getQuantity(),
                'price' => $order->getPrice(),
                'currency' => $order->getCurrency(),
                'date' => $order->getDate()?->format('Y-m-d') ?? '',
            ];
        }
        $userData['orders'] = $ordersData;

        return $userData;
    }


    /**
     * @param array $data
     * @param Order $order
     * @return Order
     * @throws Exception
     */
    private function setOrder(array $data, Order $order): Order
    {
        foreach ($data as $key => $value) {
            $methodName = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (method_exists($order, $methodName)) {
                if ($methodName === 'setDate' || $methodName === 'setOrderDate') {
                    $value = new DateTime($value);
                }

                $order->$methodName($value);
            }
        }

        return $order;
    }

    /**
     * @param array $data
     * @param User $user
     * @return User
     * @throws Exception
     */
    private function setUser(array $data, User $user): User
    {
        foreach ($data as $key => $value) {
            $methodName = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (method_exists($user, $methodName)) {
                if ($methodName === 'setDateOfBirth') {
                    $value = new DateTime($value);
                } else if ($methodName === 'setOrders') {
                    if (is_array($value)) {
                        foreach ($value as $v) {
                            $order = $this->setOrder($v, new Order());
                            //$user->addOrder($order);
                        }
                    }
                }

                $user->$methodName($value);
            }
        }

        return $user;
    }
}

