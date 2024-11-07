<?php

namespace App\Entity;

use App\ApiResource\UserApiController;
use App\DataTransformer\UserOutputProvider;
use App\Dto\UserInput;
use App\Dto\UserOutput;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Random\RandomException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            output: UserOutput::class,
            provider: UserOutputProvider::class
        ),
        new Get(
            output: UserOutput::class,
            provider: UserOutputProvider::class
        ),
        new Post(
            uriTemplate: '/user-create',
            controller: UserApiController::class,
            input: UserInput::class,
            output: UserOutput::class,
            deserialize: false
        ),
        new Put(
            uriTemplate: '/users/{id}/edit',
            uriVariables: ['id'],
            controller: UserApiController::class,
            input: UserInput::class,
            output: UserOutput::class,
            deserialize: false
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['user_read']],
    denormalizationContext: ['groups' => ['user_write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'email' => 'exact',
    'uuid' => 'exact',
    'matriculate' => 'exact',
    'firstname' => 'partial',
    'lastname' => 'partial',
])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    #[Groups(['user_read', 'user_write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user_read', 'user_write'])]
    private ?string $uuid = null;

    #[ORM\ManyToOne(targetEntity: Civility::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['user_read', 'user_write'])]
    #[MaxDepth(1)]
    private ?Civility $civility = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user_read', 'user_write'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user_read', 'user_write'])]
    private ?string $lastname = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['user_read', 'user_write'])]
    private array $roles = [];

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['user_read', 'user_write'])]
    private ?string $matriculate = null;

    #[ORM\Column(length: 100)]
    #[Groups(['user_read', 'user_write'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user_write'])]
    private ?string $password = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Groups(['user_read', 'user_write'])]
    private ?string $phone = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Groups(['user_read', 'user_write'])]
    private ?string $mobile = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user_read', 'user_write'])]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user_read', 'user_write'])]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user_read', 'user_write'])]
    private ?string $streetAddress = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user_read', 'user_write'])]
    private ?DateTime $dateOfBirth = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user_read', 'user_write'])]
    private ?string $photo = null;

    #[ORM\ManyToOne(targetEntity: CivilStatus::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?CivilStatus $civilStatus = null;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user', cascade: ['persist', 'remove'], fetch: "EXTRA_LAZY")]
    #[Groups(['user_read', 'order_read'])]
    #[MaxDepth(1)]
    private Collection $orders;

    #[ORM\ManyToMany(targetEntity: Resource::class, inversedBy: 'users', cascade: ['persist', 'remove'], fetch: "EXTRA_LAZY")]
    #[ORM\JoinTable(name: 'resource_user')]
    #[MaxDepth(1)]
    private Collection $resources;

    #[ORM\ManyToMany(targetEntity: FileToUser::class, inversedBy: 'users', fetch: "EXTRA_LAZY")]
    #[Groups(['user_read', 'user_write'])]
    #[MaxDepth(1)]
    private Collection $assignedFiles;

    #[ORM\OneToMany(targetEntity: FileToUser::class, mappedBy: 'lastModifiedBy',fetch: "EXTRA_LAZY")]
    #[Groups(['user_read'])]
    #[MaxDepth(1)]
    private Collection $modifiedFiles;

    #[Assert\NotBlank(groups: ['user_write'])]
    #[Assert\Length(min: 6, groups: ['user_write'])]
    private ?string $plainPassword = null;

    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * @throws RandomException
     */
    public function __construct()
    {
        if ($this->uuid === null) {
            $this->uuid = Uuid::v4()->toRfc4122();
        }

        if (empty($this->roles)) {
            $this->roles = ["ROLE_USER"];
        }

        if($this->matriculate === null){
            $random = strtoupper(bin2hex(random_bytes(5)));
            $this->matriculate = 'M-' . substr($random, 0, 5) . '-' . substr($random, 5, 5);
        }

        $this->assignedFiles = new ArrayCollection();
        $this->modifiedFiles = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->resources = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getMatriculate(): ?string
    {
        return $this->matriculate;
    }

    public function setMatriculate(?string $matriculate): static
    {
        $this->matriculate = $matriculate;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getStreetAddress(): ?string
    {
        return $this->streetAddress;
    }

    public function setStreetAddress(?string $streetAddress): static
    {
        $this->streetAddress = $streetAddress;
        return $this;
    }

    public function getDateOfBirth(): ?DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?DateTime $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    /**
     * @return Collection<int, FileToUser>
     */
    public function getAssignedFiles(): Collection
    {
        return $this->assignedFiles;
    }

    public function addAssignedFile(FileToUser $file): static
    {
        if (!$this->assignedFiles->contains($file)) {
            $this->assignedFiles->add($file);
        }
        return $this;
    }

    public function removeAssignedFile(FileToUser $file): static
    {
        $this->assignedFiles->removeElement($file);
        return $this;
    }

    /**
     * @return Collection<int, FileToUser>
     */
    public function getModifiedFiles(): Collection
    {
        return $this->modifiedFiles;
    }

    public function getRoles(): array
    {
        $roles = $this->roles ?? [];
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): void
    {
        $this->mobile = $mobile;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): void
    {
        $this->photo = $photo;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function setOrders(Collection $orders): void
    {
        $this->orders = $orders;
    }


    public function getResources(): Collection
    {
        return $this->resources;
    }

    public function setResources(Collection $resources): void
    {
        $this->resources = $resources;
    }


    public function getCivilStatus(): ?CivilStatus
    {
        return $this->civilStatus;
    }

    public function setCivilStatus(?CivilStatus $civilStatus): self
    {
        $this->civilStatus = $civilStatus;
        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    public function getCivility(): ?Civility
    {
        return $this->civility;
    }

    public function setCivility(Civility $civility): static
    {
        $this->civility = $civility;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'civility' => $this->getCivility()?->toArray(),
            'firstname' => $this->getFirstname(),
            'lastname' => $this->getLastname(),
            'civilStatus' => $this->getCivilStatus()?->toArray(),
            'postalCode' => $this->getPostalCode(),
            'city' => $this->getCity(),
            'email' => $this->getEmail(),
            'mobile' => $this->getMobile(),
            'phone' => $this->getPhone(),
            'streetAddress' => $this->getStreetAddress(),
            'photo' => $this->getPhoto(),
            'orders' => $this->getOrders()->map(fn(Order $order) => $order->getId())->toArray(),
            'resources' => $this->getResources()->map(fn(Resource $resource) => $resource->getId())->toArray(),
            'matriculate' => $this->getMatriculate(),
            'uuid' => $this->getUuid(),
            'roles' => $this->getRoles(),
            'assignedFiles' => $this->getAssignedFiles()->map(fn(FileToUser $file) => $file->getId())->toArray(),
            'modifiedFiles' => $this->getModifiedFiles()->map(fn(FileToUser $file) => $file->getId())->toArray(),
            'dateOfBirth' => $this->getDateOfBirth()?->format('Y-m-d'),
        ];
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}