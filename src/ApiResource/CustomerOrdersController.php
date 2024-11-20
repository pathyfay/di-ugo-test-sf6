<?php

namespace App\ApiResource;

use App\Entity\Customer;
use App\Entity\Order;
use App\Service\ImageUploadService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CustomerOrdersController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager, public ObjectNormalizer $objectNormalizer, private  ImageUploadService $imageUploadService)
    {
    }

    /**
     * @return JsonResponse
     */
    #[Route('/api/customers', name: 'get_customers', methods: ['GET'])]
    public function getCustomers(): JsonResponse
    {
        $customers = $this->entityManager->getRepository(Customer::class)->findAll();
        if (!$customers) {
            return new JsonResponse(['error' => 'Customers not found'], 404);
        }

        $customerDatas = [];
        foreach ($customers as $customer) {
            $customerData = $this->getCustomerNormalize($customer);
            $customerDatas[] = $customerData;
        }

        return new JsonResponse($customerDatas, 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/customers/{id}/orders', name: 'get_customer_orders', methods: ['GET'])]
    public function getCustomerOrders(int $id): JsonResponse
    {
        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['id' => $id]);
        if (!$customer) {
            return new JsonResponse(['error' => 'Customer not found'], 404);
        }

        $customerData = $this->getCustomerNormalize($customer);

        return new JsonResponse($customerData, 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/customers/{id}/delete', name: 'delete_customer', methods: ['DELETE'])]
    public function deleteCustomerOrders(int $id): JsonResponse
    {
        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['id' => $id]);
        if (!$customer) {
            return new JsonResponse(['error' => 'Customer not found'], 404);
        }
        $this->entityManager->remove($customer);
        $this->entityManager->flush();

        return new JsonResponse(["result"=> "OK", "status" => 200,'message' => 'Order deleted successfully'], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/api/customers/new', name: 'create_customer', methods: ['POST'])]
    public function createCustomerOrders(Request $request): JsonResponse
    {
        dump($request);
        $customer = new Customer();
        $customer->setTitle($request->request->get('title') ?? 'Mr');
        $customer->setLastname($request->request->get('lastname') ?? '');
        $customer->setFirstname($request->request->get('firstname') ?? '');
        $customer->setPostalCode($request->request->get('postalCode') ?? '');
        $customer->setCity($request->request->get('city') ?? '');
        $customer->setEmail($request->request->get('email') ?? '');
        $customer->setMobile($request->request->get('mobile') ?? '');
        $customer->setBirthday(new DateTime($request->request->get('birthday')) ?? null);
        $photo = $request->files->get('photo');
        if ($photo) {
            $imagePath = $this->imageUploadService->upload($photo);
            $customer->setPhoto($imagePath);
        }

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return new JsonResponse(["result"=> "OK", "status" => 200,'message' => 'Order created successfully'], 200);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/api/customers/{id}/edit', name: 'update_customer', methods: ['PUT'])]
    public function updateCustomerOrders(Request $request, int $id): JsonResponse
    {
        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['id' => $id]);
        if (!$customer) {
            return new JsonResponse(['error' => 'Customer not found'], 404);
        }

        $customer->setTitle($request->request->get('title') ?? $customer->getTitle());
        $customer->setLastname($request->request->get('lastname') ?? $customer->getLastname());
        $customer->setFirstname($request->request->get('firstname') ?? $customer->getFirstname());
        $customer->setPostalCode($request->request->get('postalCode') ?? $customer->getPostalCode());
        $customer->setCity($request->request->get('city') ?? $customer->getCity());
        $customer->setEmail($request->request->get('email') ?? $customer->getEmail());
        $customer->setMobile($request->request->get('mobile') ?? $customer->getMobile());
        $customer->setBirthday(new DateTime($request->request->get('birthday')) ?? $customer->getBirthday());

        $photo = $request->files->get('photo');
        if ($photo) {
            $imagePath = $this->imageUploadService->upload($photo);
            $customer->setPhoto($imagePath);
        }

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return new JsonResponse(["result"=> "OK", "status" => 200,'message' => 'Order updated successfully'], 200);
    }

    /**
     * @param Customer $customer
     * @return array
     */
    private function getCustomerNormalize(Customer $customer ): array
    {
        $ordersData = [];
        $customerData = $this->objectNormalizer->normalize($customer);
        $customerData['birthday'] = $customer->getBirthday()?->format('Y-m-d') ?? '';
        foreach ($customer->getOrders() as $order) {
            $ordersData[] = [
                'id' => $order->getId(),
                'product_id' => $order->getProduct()->getId(),
                'order_date' => $order->getOrderDate()?->format('Y-m-d') ?? '',
                'quantity' => $order->getQuantity(),
                'price' => $order->getPrice(),
                'currency' => $order->getCurrency(),
                'date' => $order->getDate()?->format('Y-m-d') ?? '',
            ];
        }
        $customerData['orders'] = $ordersData;

        return $customerData;
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
                if($methodName === 'setDate' || $methodName === 'setOrderDate'){
                    $value = new DateTime($value);
                }

                $order->$methodName($value);
            }
        }

        return $order;
    }

    /**
     * @param array $data
     * @param Customer $customer
     * @return Customer
     * @throws Exception
     */
    private function setCustomer(array $data, Customer $customer): Customer
    {
        foreach ($data as $key => $value) {
            $methodName = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (method_exists($customer, $methodName)) {
                if($methodName === 'setBirthday'){
                    $value = new DateTime($value);
                } else if($methodName === 'setOrders'){
                    if( is_array($value)) {
                        foreach ($value as $v) {
                            $order = $this->setOrder($v, new Order());
                            $customer->addOrder($order);
                        }
                    }
                }

                $customer->$methodName($value);
            }
        }

        return $customer;
    }
}

