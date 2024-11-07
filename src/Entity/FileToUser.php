<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\ApiResource\FileToUserApiController;
use App\Dto\FileToUserInput;
use App\Dto\FileToUserOutput;
use App\Enum\FileStatusEnum;
use App\Repository\FileToUserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ApiFilter(SearchFilter::class, properties: [
    'fileStatus' => 'exact',
    'fileType' => 'exact',
    'users.id' => 'exact',
    'lastModifiedBy.id' => 'exact'
])]
#[ApiResource(
    operations: [
        new GetCollection(
            paginationEnabled: true,
            paginationItemsPerPage: 100,
            paginationMaximumItemsPerPage: 200,
            order: ['dateUpdated' => 'DESC'],
            input: FileToUserInput::class,
            output: FileToUserOutput::class
        ),
        new Get(output: FileToUserOutput::class),
        new Post(
            inputFormats: ['multipart' => ['multipart/form-data']],
            controller: FileToUserApiController::class,
            deserialize: false
        ),
        new Put(
            inputFormats: ['multipart' => ['multipart/form-data']],
            controller: FileToUserApiController::class,
            deserialize: false
        )
    ],
    normalizationContext: ['groups' => ['file_user_read']],
    denormalizationContext: ['groups' => ['file_user_write']]
)]
#[ORM\Entity(repositoryClass: FileToUserRepository::class)]
class FileToUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['file_user_read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['file_user_read', 'file_user_write'])]
    private ?string $filename = null;

    #[ORM\Column(length: 255)]
    #[Groups(['file_user_read', 'file_user_write'])]
    private ?string $fileType = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Groups(['file_user_read', 'file_user_write'])]
    private ?string $extension = null;

    #[ORM\Column]
    #[Groups(['file_user_read', 'file_user_write'])]
    private ?DateTime $dateCreated = null;

    #[ORM\Column]
    #[Groups(['file_user_read', 'file_user_write'])]
    private ?DateTime $dateUpdated = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'assignedFiles')]
    #[Groups(['file_user_read', 'file_user_write'])]
    private Collection $users;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'modifiedFiles')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(['file_user_read'])]
    private ?User $lastModifiedBy = null;

    #[ORM\Column(type: 'string', enumType: FileStatusEnum::class)]
    #[Groups('file_user_read')]
    private FileStatusEnum $fileStatus = FileStatusEnum::CREATED;

    #[ORM\ManyToOne(targetEntity: FilterToFile::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['file_user_read', 'file_user_write'])]
    private FilterToFile $filterToFile;

    #[Vich\UploadableField(mapping: 'file_user', fileNameProperty: 'filename', size: 'fileSize')]
    #[Groups(['file_user_write'])]
    private ?File $file = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['file_user_read', 'file_user_write'])]
    private ?string $filePath = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['file_user_read', 'file_user_write'])]
    private ?int $fileSize = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['file_user_read', 'file_user_write'])]
    private ?string $fileOcrText = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    public function setFileType(string $fileType): static
    {
        $this->fileType = $fileType;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): static
    {
        $this->extension = $extension;

        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(DateTime $dateUpdated): static
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addAssignedFile($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeAssignedFile($this);
        }

        return $this;
    }

    public function getLastModifiedBy(): ?User
    {
        return $this->lastModifiedBy;
    }

    public function setLastModifiedBy(?User $lastModifiedBy): static
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    public function getFileStatus(): FileStatusEnum
    {
        return $this->fileStatus;
    }

    public function setFileStatus(FileStatusEnum $fileStatus): static
    {
        $this->fileStatus = $fileStatus;

        return $this;
    }

    public function getFilterToFile(): FilterToFile
    {
        return $this->filterToFile;
    }

    public function setFilterToFile(FilterToFile $filterToFile): static
    {
        $this->filterToFile = $filterToFile;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): static
    {
        $this->file = $file;

        if ($file) {
            $this->dateUpdated = new DateTime();
        }

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(?int $fileSize): static
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getFileOcrText(): ?string
    {
        return $this->fileOcrText;
    }

    public function setFileOcrText(?string $fileOcrText): static
    {
        $this->fileOcrText = $fileOcrText;

        return $this;
    }
}
