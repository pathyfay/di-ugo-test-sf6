<?php

namespace App\DataTransformer;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\ProductInput;
use App\Entity\Product;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'input', priority: 10)]
final class ProductInputDataTransformer implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof ProductInput) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        /** @var Product $product */
        $product = $context['object_to_populate'] ?? new Product();

        $product->setNom($data->nom());
        $product->setShortNom($data->shortNom);
        $product->setReference($data->reference);
        $product->setDescription($data->description);
        $product->setPrice($data->price);
        $product->setCreatedAt($data->createdAt);
        $product->setUpdatedAt($data->updatedAt);
        $product->setCurrency($data->currency);
        $product->setCategories($data->category);
        $product->setStocks($data->stock);
        $product->setPicture($data->picture);

        return $this->persistProcessor->process($product, $operation, $uriVariables, $context);
    }
}