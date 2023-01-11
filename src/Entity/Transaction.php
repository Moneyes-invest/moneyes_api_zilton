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
use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['get:transactions']]),
        new Get(normalizationContext: ['groups' => ['get:transactions', 'get:transaction']]),
        new Post(),
        new Put(),
        new Patch(),
        new Delete(),
    ],
    denormalizationContext: ['groups' => ['create:transaction', 'update:transaction']],
)]
class Transaction
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['get:transaction', 'get:transactions'])]
    private Uuid $id;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['get:transaction', 'create:transaction'])]
    #[Assert\NotBlank(groups: ['create:transaction'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    #[Groups(['get:transaction', 'create:transaction'])]
    #[Assert\NotBlank(groups: ['create:transaction'])]
    private ?float $value = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['get:transaction', 'create:transaction', 'get:exchanges'])]
    private ?Exchange $exchange = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['get:transaction', 'create:transaction'])]
    #[Assert\NotBlank(groups: ['create:transaction'])]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Groups(['get:transaction'])]
    private ?string $type = 'SPOT';

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['get:transaction', 'create:transaction'])]
    #[Assert\NotBlank(groups: ['create:transaction'])]
    private ?Currency $currency = null;

    #[ORM\Column(length: 255)]
    #[Groups(['get:transaction', 'create:transaction'])]
    #[Assert\NotBlank(groups: ['create:transaction'])]
    private string $orderDirection = '';

    #[ORM\Column]
    #[Groups(['get:transaction', 'create:transaction'])]
    #[Assert\NotBlank(groups: ['create:transaction'])]
    private int $amount;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getExchange(): ?Exchange
    {
        return $this->exchange;
    }

    public function setExchange(?Exchange $exchange): self
    {
        $this->exchange = $exchange;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Specify transaction type (Spot, Futures, Margin, etc.) Type is different between exchanges and currency.
     *
     * @param string $type (default = "SPOT" for 0.0.1-pre-alpha)
     *
     * @return $this
     */
    public function setType(string $type = 'SPOT'): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }

    /**
     * Can be "BUY", "SELL" or "TRANSFERT".
     *
     * @return $this
     */
    public function setOrderDirection(string $orderDirection = 'BUY'): self
    {
        $this->orderDirection = $orderDirection;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
