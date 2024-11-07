<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\OrderOutput;
use App\Dto\UserInput;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class OrderOutputDataTransformer implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): OrderOutput
    {
        if (!$data instanceof UserInput) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        /** @var OrderOutput $orderOutput */
        $orderOutput = $context['object_to_populate'] ?? new OrderOutput();
        
        $orderOutput->id = $data->getId();
        $orderOutput->userId = $data->getUser()?->getId();
        $orderOutput->productIds = array_map(fn($product) => $product->getId(), $data->getProducts()->toArray());
        $orderOutput->currency = $data->getCurrency();
        $orderOutput->price = $data->getPrice();
        $orderOutput->quantity = $data->getQuantity();
        $orderOutput->orderDate = $data->getOrderDate();
        $orderOutput->date = $data->getCreatedAt();

        return $this->persistProcessor->process($orderOutput, $operation, $uriVariables, $context);
    }
}