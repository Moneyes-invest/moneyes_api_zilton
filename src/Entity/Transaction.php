<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?float $value = null;

    #[ORM\ManyToOne]
    private ?Exchange $id_exchange = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $id_user = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Currency $id_currency = null;

    #[ORM\Column(length: 255)]
    private string $order_direction = "";

    public function getId(): ?int
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

    public function getIdExchange(): ?Exchange
    {
        return $this->id_exchange;
    }

    public function setIdExchange(?Exchange $id_exchange): self
    {
        $this->id_exchange = $id_exchange;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(?User $id_user): self
    {
        $this->id_user = $id_user;

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

    public function getIdCurrency(): ?Currency
    {
        return $this->id_currency;
    }

    public function setIdCurrency(?Currency $id_currency): self
    {
        $this->id_currency = $id_currency;

        return $this;
    }

    public function getOrderDirection(): array
    {
        return $this->order_direction;
    }

    public function setOrderDirection(array $order_direction): self
    {
        $this->order_direction = $order_direction;

        return $this;
    }
}
