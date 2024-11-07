<?php

namespace App\Dto;

use App\Enum\FileStatusEnum;
use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;

final class FileToUserOutput
{
    #[Groups(['file_to_process_read'])]
    public ?int $id = null;

    #[Groups(['file_to_process_read'])]
    public ?string $filename = null;

    #[Groups(['file_to_process_read'])]
    public ?string $fileType = null;

    #[Groups(['file_to_process_read'])]
    public ?string $extension = null;

    #[Groups(['file_to_process_read'])]
    public ?DateTime $dateCreated = null;

    #[Groups(['file_to_process_read'])]
    public ?DateTime $dateUpdated = null;

    #[Groups(['file_to_process_read'])]
    public ?string $filePath = null;

    #[Groups(['file_to_process_read'])]
    public ?int $fileSize = null;

    #[Groups(['file_to_process_read'])]
    public ?string $fileOcrText = null;

    #[Groups(['file_to_process_read'])]
    public FileStatusEnum|string|null $fileStatus = null;

    #[Groups(['file_to_process_read'])]
    public mixed $fileModel = null;

    #[Groups(['file_to_process_read'])]
    public array $users = [];

    #[Groups(['file_to_process_read'])]
    public mixed $lastModifiedBy = null;
}
