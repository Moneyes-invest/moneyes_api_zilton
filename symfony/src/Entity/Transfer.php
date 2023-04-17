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
use App\Repository\TransferRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    operations: [
        new GetCollection(
            order: ['date' => 'DESC'],
            normalizationContext: ['groups' => ['get:transfers']]
        ),
        new Get(normalizationContext: ['groups' => ['get:transfers', 'get:transfer']], security: 'object.getUser() == user or is_granted("ROLE_ADMIN")'),
        new Post(
            normalizationContext: ['groups' => ['get:transfers', 'get:transfer']],
            denormalizationContext: ['groups' => ['post:transfers']],
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['account' => 'exact', 'asset' => 'exact'])]
#[ORM\Entity(repositoryClass: TransferRepository::class)]
class Transfer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Asset $asset = null;

    #[ORM\Column]
    private ?float $quantity = null;

    #[ORM\Column(nullable: true)]
    private ?float $fees = null;

    #[ORM\ManyToOne]
    private ?Asset $assetFees = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $externalTransferId = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'transfers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getFees(): ?float
    {
        return $this->fees;
    }

    public function setFees(?float $fees): self
    {
        $this->fees = $fees;

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

    public function getExternalTransferId(): ?string
    {
        return $this->externalTransferId;
    }

    public function setExternalTransferId(?string $externalTransferId): self
    {
        $this->externalTransferId = $externalTransferId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
