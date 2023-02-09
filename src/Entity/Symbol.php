<?php

namespace App\Entity;

use App\Repository\SymbolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SymbolRepository::class)]
class Symbol
{
    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\ManyToMany(targetEntity: Exchange::class, inversedBy: 'symbols')]
    private Collection $exchange;

    public function __construct()
    {
        $this->exchange = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Exchange>
     */
    public function getExchange(): Collection
    {
        return $this->exchange;
    }

    public function addExchange(Exchange $exchange): self
    {
        if (!$this->exchange->contains($exchange)) {
            $this->exchange->add($exchange);
        }

        return $this;
    }

    public function removeExchange(Exchange $exchange): self
    {
        $this->exchange->removeElement($exchange);

        return $this;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
