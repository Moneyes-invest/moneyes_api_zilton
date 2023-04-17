<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            order: ['date' => 'DESC'],
            normalizationContext: ['groups' => ['get:transactions']],
        ),
        new Get(normalizationContext: ['groups' => ['get:transaction']], security: 'object.getAccount().getUser() == user or is_granted("ROLE_ADMIN")'),
        new Post(
            normalizationContext: ['groups' => ['get:transaction']],
            denormalizationContext: ['groups' => ['create:transaction']],
        ),
    ],
)]
#[ApiFilter(SearchFilter::class, strategy: 'exact', properties: ['account' => 'exact', 'asset' => 'exact'])]
#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['get:transaction', 'get:transactions'])]
    private Uuid $id;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['get:transaction', 'get:transactions', 'create:transaction'])]
    #[Assert\NotBlank(groups: ['create:transaction'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['get:transaction', 'create:transaction'])]
    private ?Account $account;

    #[ORM\Column(length: 255)]
    #[Groups(['get:transaction'])]
    private string $type = 'SPOT';

    #[ORM\Column(length: 255)]
    #[Groups(['get:transaction', 'get:transactions', 'create:transaction'])]
    #[Assert\NotBlank(groups: ['create:transaction'])]
    private string $orderDirection = 'BUY';

    #[ORM\Column]
    private float $fees = 0;

    #[ORM\Column]
    #[Groups(['get:transaction', 'get:transactions', 'create:transaction'])]
    #[Assert\NotBlank(groups: ['create:transaction'])]
    private float $price;

    #[ORM\Column]
    #[Groups(['get:transaction', 'get:transactions', 'create:transaction'])]
    #[Assert\NotBlank(groups: ['create:transaction'])]
    private float $quantity;

    #[ORM\Column(nullable: true)]
    private ?string $externalTransactionId = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Symbol $symbol;

    #[ORM\ManyToOne]
    private ?Asset $assetFees = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['get:transaction', 'get:transactions', 'create:transaction'])]
    private ?Asset $asset = null;

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

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

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

    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }

    /**
     * Can be "BUY", "SELL", "TRANSFER", "WITHDRAW", "DEPOSIT".
     *
     * @return $this
     */
    public function setOrderDirection(string $orderDirection = 'BUY'): self
    {
        $this->orderDirection = $orderDirection;

        return $this;
    }

    public function getFees(): float
    {
        return $this->fees;
    }

    public function setFees(float $fees): self
    {
        $this->fees = $fees;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getExternalTransactionId(): ?string
    {
        return $this->externalTransactionId;
    }

    public function setExternalTransactionId(?string $externalTransactionId): self
    {
        $this->externalTransactionId = $externalTransactionId;

        return $this;
    }

    public function getSymbol(): ?Symbol
    {
        return $this->symbol;
    }

    public function setSymbol(Symbol $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getAssetFees(): ?Asset
    {
        return $this->assetFees;
    }

    public function setAssetFees(?Asset $assetFees): self
    {
        $this->assetFees = $assetFees;

        return $this;
    }

    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    public function setAsset(?Asset $asset): self
    {
        $this->asset = $asset;

        return $this;
    }
}
