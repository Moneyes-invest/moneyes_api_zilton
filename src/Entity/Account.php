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
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\AccountRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['get:accounts']]),
        new Get(normalizationContext: ['groups' => ['get:accounts', 'get:account']]),
        new Post(),
        new Put(),
        new Patch(),
        new Delete(),
    ],
    denormalizationContext: ['groups' => ['create:account', 'update:account']],
)]
class Account
{
    const EXCHANGE = null;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['get:account', 'get:accounts'])]
    private Uuid $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['get:account', 'create:account'])]
    #[Assert\NotBlank(groups: ['create:account'])]
    private Exchange $exchange;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get:account', 'create:account'])]
    #[Assert\NotBlank(groups: ['create:account'])]
    private ?string $privateKey = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get:account', 'create:account'])]
    #[Assert\NotBlank(groups: ['create:account'])]
    private ?string $publicKey = null;

    #[ORM\ManyToOne(inversedBy: 'account')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['get:account', 'create:account'])]
    #[Assert\NotBlank(groups: ['create:account'])]
    private User $user;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getExchange(): Exchange
    {
        return $this->exchange;
    }

    public function setExchange(Exchange $exchange): self
    {
        $this->exchange = $exchange;

        return $this;
    }

    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    public function setPrivateKey(?string $privateKey): self
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    public function setPublicKey(?string $publicKey): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    public function getRepositoryClass(): string
    {
        return $this->exchange->getLabel() ? 'App\\Repository\\' . $this->exchange->getLabel() . 'AccountRepository' : AccountRepository::class;
    }
}
