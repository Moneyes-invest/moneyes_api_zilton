<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code_iso = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeIso(): ?string
    {
        return $this->code_iso;
    }

    public function setCodeIso(string $code_iso): self
    {
        $this->code_iso = $code_iso;

        return $this;
    }
}
