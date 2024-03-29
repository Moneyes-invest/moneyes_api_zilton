<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use App\State\DashboardProvider;
use App\State\ProfileProvider;
use App\State\UserPasswordHasher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/profile',
            security: 'is_authenticated()',
            name: 'get_profile',
            provider: ProfileProvider::class,
        ),
        new Get(
            uriTemplate: '/user/dashboard',
            security: 'is_authenticated()',
            provider: DashboardProvider::class,
        ),
        new Post(uriTemplate: '/register', processor: UserPasswordHasher::class),
        new Delete(uriTemplate: '/user/{id}', security: 'is_granted("ROLE_ADMIN")'),
    ],
    normalizationContext: ['groups' => [self::GROUP_READ]],
    denormalizationContext: ['groups' => [self::GROUP_CREATE, self::GROUP_UPDATE]],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const GROUP_READ   = 'user:read';
    public const GROUP_CREATE = 'user:create';
    public const GROUP_UPDATE = 'user:update';

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups([self::GROUP_READ])]
    private Uuid $id;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(groups: ['user:create'])]
    #[Assert\Email]
    #[Groups([self::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    private string $email;

    #[ORM\Column]
    #[Assert\NotBlank(groups: ['user:create'])]
    #[Groups([self::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    private array $roles = [];

    /** @var string The hashed password */
    #[ORM\Column]
    private string $password;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([self::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    private string $username;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups([self::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    private \DateTimeInterface $birthdate;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['user:create'])]
    #[Groups([self::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    private string $name;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['user:create'])]
    #[Groups([self::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    private string $lastname;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Account::class)]
    private Collection $account;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(groups: ['user:create'])]
    #[Groups([self::GROUP_CREATE, self::GROUP_UPDATE])]
    private ?string $plainPassword = null;

    #[ORM\OneToMany(mappedBy: 'userDevice', targetEntity: Device::class)]
    private Collection $devices;

    public function __construct()
    {
        $this->account = new ArrayCollection();
        $this->devices = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getBirthdate(): \DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection<int, Account>
     */
    public function getAccount(): Collection
    {
        return $this->account;
    }

    public static function createFromPayload(string $username, array $payload): void
    {
        // TODO: Implement createFromPayload() method.
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return Collection<int, Device>
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(Device $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices->add($device);
            $device->setUserDevice($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        if ($this->devices->removeElement($device)) {
            // set the owning side to null (unless already changed)
            if ($device->getUserDevice() === $this) {
                $device->setUserDevice(null);
            }
        }

        return $this;
    }
}
