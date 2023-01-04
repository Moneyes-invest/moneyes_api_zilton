<?php

namespace App\Entity;

use App\Repository\HoldingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HoldingRepository::class)]
class Holding
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'holdings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $idUser = null;

    #[ORM\ManyToOne(inversedBy: 'holdings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Currency $idCurrency = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exchange $idExchange = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $averagePurchasePrice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdCurrency(): ?Currency
    {
        return $this->idCurrency;
    }

    public function setIdCurrency(?Currency $idCurrency): self
    {
        $this->idCurrency = $idCurrency;

        return $this;
    }

    public function getIdExchange(): ?Exchange
    {
        return $this->idExchange;
    }

    public function setIdExchange(?Exchange $idExchange): self
    {
        $this->idExchange = $idExchange;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getAveragePurchasePrice(): ?string
    {
        return $this->averagePurchasePrice;
    }

    public function setAveragePurchasePrice(string $averagePurchasePrice): self
    {
        $this->averagePurchasePrice = $averagePurchasePrice;

        return $this;
    }
}
