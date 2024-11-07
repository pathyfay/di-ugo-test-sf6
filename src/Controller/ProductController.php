<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Traits\UtilsTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ProductController extends AbstractController
{
    use UtilsTrait;
    public function __construct(
        public EntityManagerInterface $entityManager,
        public ObjectNormalizer $objectNormalizer
    ){
    }

    #[Route('/products', name: 'product_list')]
    public function list(): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        $datas = array_filter(
            array_map(fn($p) => $p?->toArray(), $products)
        );

        return $this->render('lists.html.twig', [
            'datas' => $datas,
            'title' => 'product',
        ]);

    }

    #[Route('/products-create', name: 'product_create')]
    #[Route('/products/{id}/edit', name: 'product_edit')]
    public function new_edit(Request $request, Product $product = null): Response
    {
        if ($product === null) {
            $product = new product();
        } else {
            $product = $this->entityManager->getRepository(Product::class)->find(['id' => $product->getId()]);
            if (!$product) {
                throw $this->createNotFoundException('product not found');
            }
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$product->getId()) {
                $this->entityManager->persist($product);
            }
            $this->entityManager->flush();

            return $this->redirectToRoute('product_list');
        }

        return $this->render('form/new_edit.html.twig', [
            'form' => $form->createView(),
            'objectForm' => $product,
            'title' => 'product'
        ]);
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function show(Product $product): Response
    {
        $product = $this->entityManager->getRepository(product::class)->findOneBy(['id' => $product->getId()]);
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('/products/{id}/delete', name: 'product_delete')]
    public function delete(Product $product): RedirectResponse
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->redirectToRoute('product_list');
    }
}
