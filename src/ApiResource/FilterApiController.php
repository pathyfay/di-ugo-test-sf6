<?php

namespace App\ApiResource;

use App\Entity\FilterToFile;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class FilterApiController
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security               $security,
        private LoggerInterface        $logger
    ){
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            throw new NotFoundHttpException('Invalid JSON body.');
        }

        $name = $data['name'] ?? '';
        $regex = $data['regex'] ?? '';
        $status = $data['status'] ?? '';
        $priority = (int)($data['priority'] ?? 0);
        $id = $data['id'] ?? null;

        if ($request->getMethod() === 'POST' || !$id) {
            $filter = new FilterToFile();
            $statusCode = 201;
        } else {
            $filter = $this->em->getRepository(FilterToFile::class)->find($id);

            if (!$filter) {
                throw new NotFoundHttpException("FilterToFile with ID $id not found.");
            }
            $statusCode = 200;
        }

        $filter->setName($name);
        $filter->setRegex($regex);
        $filter->setStatus($status);
        $filter->setPriority($priority);
        $filter->setDateCreated(new DateTime());

        $this->em->persist($filter);
        $this->em->flush();

        $this->logger->log('info', 'Log FilterToFile saved/updated', [
            'name' => $filter->getName(),
            'regex' => $filter->getRegex(),
            'status' => $filter->getStatus(),
            'priority' => $filter->getPriority(),
            'dateCreated' => $filter->getDateCreated()->format('Y-m-d H:i:s'),
        ]);

        $message = $statusCode === 201
            ? 'Filtre enregistré avec succès'
            : 'Filtre mis à jour avec succès';

        return new JsonResponse([
            'message' => $message,
            'id' => $filter->getId(),
        ], $statusCode);
    }
}
