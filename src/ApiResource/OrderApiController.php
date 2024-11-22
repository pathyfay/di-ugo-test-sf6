<?php

namespace App\ApiResource;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\Product;
use App\Service\ImageUploadService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class OrderApiController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager, public ObjectNormalizer $objectNormalizer, private  ImageUploadService $imageUploadService)
    {
    }

    /**
     * @return JsonResponse
     */
    #[Route('/api/orders', name: 'get_orders', methods: ['GET'])]
    public function getOrders(): JsonResponse
    {
        $orders = $this->entityManager->getRepository(Order::class)->findAll();
        if (!$orders) {
            return new JsonResponse(['error' => 'Orders not found'], 404);
        }

        $orderDatas = [];
        foreach ($orders as $order) {
            $orderData = $this->getOrderNormalize($order);
            $orderDatas[] = $orderData;
        }

        return new JsonResponse($orderDatas, 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/orders/{id}', name: 'get_order_by_id', methods: ['GET'])]
    public function getOrderById(int $id): JsonResponse
    {
        $orders = $this->entityManager->getRepository(Order::class)->findOneBy(['id' => $id]);
        if (!$orders) {
            return new JsonResponse(['error' => 'Order not found'], 404);
        }

        $orderData = $this->getOrderNormalize($orders);

        return new JsonResponse($orderData, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/api/orders/new', name: 'create_order', methods: ['POST'])]
    public function createOrder(Request $request): JsonResponse
    {
        $order = new Order();
        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['id' => $request->request->get('customerId')]);
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $request->request->get('productId')]);
        $order->setOrderDate(new DateTime($request->request->get('orderDate') ?? null));
        $order->setDate(new DateTime($request->request->get('date') ?? null));
        $order->setPrice($request->request->get('price') ?? 0);
        $order->setCustomer($customer);
        $order->setCurrency($request->request->get('currency') ?? 'euros');
        $order->setQuantity($request->request->get('quantity') ?? 0);
        $order->setProduct($product);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return new JsonResponse(["result"=> "OK", "status" => 200,'message' => 'Order created successfully'], 200);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/api/orders/{id}/edit', name: 'update_order', methods: ['PUT'])]
    public function updateOrder(Request $request, int $id): JsonResponse
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['id' => $id]);
        if (!$order) {
            return new JsonResponse(['error' => 'Order not found'], 404);
        }

        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['id' => $request->request->get('customerId')]);
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $request->request->get('productId')]);
        $order->setOrderDate(new DateTime($request->request->get('orderDate') ?? $order->getOrderDate()));
        $order->setDate(new DateTime($request->request->get('date') ?? $order->getDate()));
        $order->setPrice($request->request->get('price') ?? $order->getQuantity());
        $order->setCustomer($customer ?? $order->getCustomer());
        $order->setCurrency($request->request->get('currency') ?? $order->getCurrency());
        $order->setQuantity($request->request->get('quantity') ?? $order->getQuantity());
        $order->setProduct($product ?? $order->getProduct());

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return new JsonResponse(["result"=> "OK", "status" => 200,'message' => 'Order updated successfully'], 200);
    }

    /**
     * @param Order $order
     * @return array
     */
    private function getOrderNormalize(Order $order ): array
    {
        return [
                'id' => $order->getId(),
                'customer_id' => $order->getCustomer()?->getId(),
                'product_id' => $order->getProduct()?->getId(),
                'order_date' => $order->getOrderDate()?->format('Y-m-d') ?? '',
                'quantity' => $order->getQuantity(),
                'price' => $order->getPrice(),
                'currency' => $order->getCurrency(),
                'date' => $order->getDate()?->format('Y-m-d') ?? '',
            ];
    }
}

