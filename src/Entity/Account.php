<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'account', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $id_user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exchange $id_exchange = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $private_key = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $public_key = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(User $id_user): self
    {
        $this->id_user = $id_user;

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

    public function getPrivateKey(): ?string
    {
        return $this->private_key;
    }

    public function setPrivateKey(?string $private_key): self
    {
        $this->private_key = $private_key;

        return $this;
    }

    public function getPublicKey(): ?string
    {
        return $this->public_key;
    }

    public function setPublicKey(?string $public_key): self
    {
        $this->public_key = $public_key;

        return $this;
    }
}
