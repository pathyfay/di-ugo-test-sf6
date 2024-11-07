<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\ApiResource\FilterApiController;
use App\Dto\FilterToFileInput;
use App\Dto\FilterToFileOutput;
use App\Repository\FilterToFileRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'regex' => 'exact',
    'status' => 'exact',
])]
#[ApiResource(
    operations: [
        new GetCollection(
            paginationEnabled: true,
            paginationItemsPerPage: 10,
            paginationMaximumItemsPerPage: 100,
            paginationClientItemsPerPage: true,
            order: ['dateCreated' => 'DESC'],
            input: FilterToFileInput::class,
            output: FilterToFileOutput::class
        ),
        new Get(output: FilterToFileOutput::class),
        new Post(
            controller: FilterApiController::class,
            deserialize: false,
        ),
        new Put(
            controller: FilterApiController::class,
            deserialize: false,
        ),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['filter_to_file_read']],
    denormalizationContext: ['groups' => ['filter_to_file_write']]
)
]
#[ORM\Entity(repositoryClass: FilterToFileRepository::class)]
#[ORM\HasLifecycleCallbacks]
class FilterToFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['filter_to_file_read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['filter_to_file_read', 'filter_to_file_write'])]
    private ?string $name = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['filter_to_file_read', 'filter_to_file_write'])]
    private ?string $regex = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['filter_to_file_read', 'filter_to_file_write'])]
    private ?string $status = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['filter_to_file_read', 'filter_to_file_write'])]
    private ?DateTime $dateCreated = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['filter_to_file_read', 'filter_to_file_write'])]
    private int $priority = 0;

    #[ORM\PrePersist]
    public function initializeDateCreated(): void
    {
        if ($this->dateCreated === null) {
            $this->dateCreated = new DateTime();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getRegex(): ?string
    {
        return $this->regex;
    }

    public function setRegex(?string $regex): self
    {
        $this->regex = $regex;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?DateTime $dateCreated): self
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }
}
