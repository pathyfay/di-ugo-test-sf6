<?php

namespace App\ApiResource;

use App\Entity\Order;
use App\Entity\Product;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ProductApiController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager, public ObjectNormalizer $objectNormalizer, public  ImageUploadService $imageUploadService)
    {
    }

    /**
     * @return JsonResponse
     */
    #[Route('/api/products', name: 'get_products', methods: ['GET'])]
    public function getProducts(): JsonResponse
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        if (!$products) {
            return new JsonResponse(['error' => 'products not found'], 404);
        }

        $productDatas = [];
        foreach ($products as $product) {
            $productData = $this->getproductNormalize($product);
            $productDatas[] = $productData;
        }

        return new JsonResponse($productDatas, 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/products/{id}', name: 'get_product_by_id', methods: ['GET'])]
    public function getProductById(int $id): JsonResponse
    {
        $products = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);
        if (!$products) {
            return new JsonResponse(['error' => 'product not found'], 404);
        }

        $productData = $this->getproductNormalize($products);

        return new JsonResponse($productData, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/api/products/new', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $product = new product();
       // $order = $this->entityManager->getRepository(Order::class)->findOneBy(['id' => $request->request->get('orderId')]);
        $product->setNom($request->request->get('nom') ?? null);
        $product->setShortNom($request->request->get('shortNom') ?? null);
        $product->setReference($request->request->get('reference') ?? null);
        $product->setPrice($request->request->get('price') ?? 0);
        $product->setDescription($request->request->get('description'));

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return new JsonResponse(["result"=> "OK", "status" => 200,'message' => 'product created successfully'], 200);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/api/products/{id}/edit', name: 'update_product', methods: ['PUT'])]
    public function updateProduct(Request $request, int $id): JsonResponse
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);
        if (!$product) {
            return new JsonResponse(['error' => 'product not found'], 404);
        }

        $product->setNom($request->request->get('nom') ?? $product->getNom());
        $product->setReference($request->request->get('reference') ?? $product->getReference());
        $product->setPrice($request->request->get('price') ?? $product->getPrice());
        $product->setDescription($request->request->get('description') ?? $product->getDescription());

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return new JsonResponse(["result"=> "OK", "status" => 200,'message' => 'product updated successfully'], 200);
    }

    /**
     * @param Product $product
     * @return array
     */
    private function getProductNormalize(Product $product ): array
    {
        return [
            'id' => $product->getId(),
            'nom' => $product->getNom(),
            'shortNom' => $product->getShortNom(),
            'reference' => $product->getReference(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'orders' => [] //$product->getOrders()
        ];
    }
}

