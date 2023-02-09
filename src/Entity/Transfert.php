<?php

namespace App\Entity;

use App\Repository\TransfertRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransfertRepository::class)]
class Transfert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $date = null;

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
    private ?string $externalTransfertId = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

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

    public function getExternalTransfertId(): ?string
    {
        return $this->externalTransfertId;
    }

    public function setExternalTransfertId(?string $externalTransfertId): self
    {
        $this->externalTransfertId = $externalTransfertId;

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

}
