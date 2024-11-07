<?php

namespace App\Dto;

use App\Enum\FileStatusEnum;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

final class FileToUserInput
{
    #[Assert\NotNull(groups: ['file_to_process_write'])]
    #[Assert\File(maxSize: '30M', mimeTypesMessage: "Le type de fichier n'est pas trop lourd.")]
    #[Groups(['file_to_process_write'])]
    public ?UploadedFile $file = null;

    #[Groups(['file_to_process_write'])]
    public ?string $filename = null;

    #[Groups(['file_to_process_write'])]
    public ?string $fileType = null;

    #[Groups(['file_to_process_write'])]
    public ?string $extension = null;

    #[Groups(['file_to_process_write'])]
    public ?int $fileSize = null;

    #[Groups(['file_to_process_write'])]
    public ?string $filePath = null;

    #[Assert\NotBlank(groups: ['file_to_process_write'])]
    #[Groups(['file_to_process_write'])]
    public ?int $fileModel = null;

    #[Assert\Choice(
        choices: [FileStatusEnum::CREATED, FileStatusEnum::COMPLETED, FileStatusEnum::BLOCKED, FileStatusEnum::PENDING, FileStatusEnum::IN_PROGRESS, FileStatusEnum::FAILED, FileStatusEnum::ARCHIVED, FileStatusEnum::DELETED, FileStatusEnum::DONE, FileStatusEnum::DONE],
        message: 'Le statut "{{ value }}" n’est pas valide.',
        groups: ['file_to_process_write']
    )]
    #[Groups(['file_to_process_write'])]
    public FileStatusEnum $fileStatus = FileStatusEnum::CREATED;
}
