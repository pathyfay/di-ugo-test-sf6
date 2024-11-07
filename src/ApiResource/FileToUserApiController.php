<?php

namespace App\ApiResource;

use App\Entity\FileToUser;
use App\Entity\FilterToFile;
use App\Entity\User;
use App\Enum\FileStatusEnum;
use App\Service\FileUploaderService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class FileToUserApiController
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security               $security,
        private FileUploaderService    $fileUploader,
        private LoggerInterface        $logger
    ){}

    public function __invoke(Request $request, ?FileToUser $data = null): JsonResponse
    {
        $isPut = $request->getMethod() === 'PUT';
        if ($isPut) {
            if (!$data) {
                $id = $request->attributes->get('id');
                $data = $this->em->getRepository(FileToUser::class)->find($id);
            }
            if (!$data) {
                throw new NotFoundHttpException('Fichier à mettre à jour non trouvé.');
            }
        }

        $fields       = $request->request;
        $statusStr = $this->readStr($request, 'file_status', FileStatusEnum::CREATED->value);
        $status = FileStatusEnum::tryFrom($statusStr) ?? FileStatusEnum::CREATED;

        $type      = $this->readStr($request, 'file_type', 'application/pdf');
        $extension = $this->readStr($request, 'extension', 'pdf');
        $modelName = $this->readStr($request, 'filter_to_file', '');
        $computedSize = $this->readStr($request, 'file_size', '0');
        $model = $this->em->getRepository(FilterToFile::class)->findOneBy(['name' => $modelName]) ?? $this->em->getRepository(FilterToFile::class)->findOneBy(['name' => 'Autres']);
        if (!$model) {
            throw new BadRequestHttpException("Modèle de fichier introuvable (ni '{$modelName}', ni 'Autres').");
        }

        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('file');
        $statusCode   = $isPut ? 200 : 201;

        if(!$isPut) {
            if (!$uploadedFile instanceof UploadedFile || !$uploadedFile->isValid() || $uploadedFile->getSize() === 0) {
                throw new BadRequestHttpException('Fichier manquant ou vide pour la création.');
            }

            $publicPath = $this->fileUploader->upload($uploadedFile, $model->getName());
            if (!$publicPath) {
                throw new BadRequestHttpException('Erreur lors du déplacement du fichier.');
            }
            $fileToUser = new FileToUser();
            $original = $uploadedFile->getClientOriginalName() ?: ($fileToUser->getFilename() ?? 'upload');
            $ext = pathinfo($original, PATHINFO_EXTENSION) ?: ($uploadedFile->guessExtension() ?: 'pdf');
            if ($ext === 'jpeg') { $ext = 'jpg'; }
        } else { // PUT
            $fileToUser = $data;
            $publicPath= $data->getFilePath();
        }

        $dateCreatedStr = $isPut
            ? ($fileToUser->getDateCreated()?->format('YmdHis') ?? date('YmdHis'))
            : date('YmdHis');

        $extension = $extension ?: 'pdf';
        $filenameFinal = strtolower($model->getName()) . '_' . $dateCreatedStr . '.' . $extension;

        $fileToUser->setFilename($filenameFinal);
        $fileToUser->setFilterToFile($model);
        $fileToUser->setFilePath($publicPath);
        $fileToUser->setExtension($extension);
        $fileToUser->setFileStatus($status);
        $fileToUser->setFileType($type);
        $fileToUser->setFileSize($computedSize);

        if (!$isPut) {
            $fileToUser->setDateCreated(new DateTime());
        }
        $fileToUser->setDateUpdated(new DateTime());

        $userFromToken = $this->security->getUser();
        if ($userFromToken) {
            $user = $this->em->getRepository(User::class)->find($userFromToken->getId());
            $fileToUser->setLastModifiedBy($user);
        }

        $this->em->persist($fileToUser);
        $this->em->flush();

        $this->logger->warning('FileToUserApiController :: upload', [
            'method'    => $isPut ? 'PUT' : 'POST',
            'model'     => $model->getName(),
            'status'    => $status->value,
            'type'      => $type,
            'extension' => $extension,
            'size'      => $computedSize,
            'filename'  => $filenameFinal,
            'path'      => $publicPath,
            'hasFile'   => $request->files->has('file'),
            'file_keys'    => array_keys($request->files->all()),
            'form_fields'  => $request->request->all(),
            'query'        => $request->query->all()
        ]);

        $message = $isPut ? 'Fichier mis à jour avec succès' : 'Fichier enregistré avec succès';
        return new JsonResponse(['message' => $message, 'id' => $fileToUser->getId()], $statusCode);
    }

    private function readStr(Request $req, string $key, string $default = ''): string
    {
        if ($req->request->has($key)) {
            return (string) $req->request->get($key);
        }

        if ($req->query->has($key)) {
            return (string) $req->query->get($key);
        }

        $h = 'X-'.str_replace(' ', '-', ucwords(str_replace('_',' ', $key)));
        if ($v = $req->headers->get($h)) {
            return $v;
        }

        $ct = (string) $req->headers->get('Content-Type', '');
        if (str_starts_with(strtolower($ct), 'application/json')) {
            $json = json_decode($req->getContent(), true);
            if (is_array($json) && array_key_exists($key, $json)) {
                return (string) $json[$key];
            }
        }

        return $default;
    }
}