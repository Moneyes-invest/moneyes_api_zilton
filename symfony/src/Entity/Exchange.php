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
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ExchangeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExchangeRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['get:exchanges']]),
        new Get(normalizationContext: ['groups' => ['get:exchanges', 'get:exchange']]),
    ],
    denormalizationContext: ['groups' => ['create:exchange', 'update:exchange']],
)]
class Exchange
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['get:exchanges'])]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['create:exchange'])]
    #[Groups(['create:exchange', 'get:exchanges'])]
    private string $label;

    #[ORM\ManyToMany(targetEntity: Asset::class, mappedBy: 'exchange')]
    private Collection $assets;

    #[ORM\ManyToMany(targetEntity: Symbol::class, mappedBy: 'exchange')]
    private Collection $symbols;

    public function __construct()
    {
        $this->assets  = new ArrayCollection();
        $this->symbols = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->label;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, Asset>
     */
    public function getAssets(): Collection
    {
        return $this->assets;
    }

    public function addAsset(Asset $asset): self
    {
        if (!$this->assets->contains($asset)) {
            $this->assets->add($asset);
            $asset->addExchange($this);
        }

        return $this;
    }

    public function removeAsset(Asset $asset): self
    {
        if ($this->assets->removeElement($asset)) {
            $asset->removeExchange($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Symbol>
     */
    public function getSymbols(): Collection
    {
        return $this->symbols;
    }

    public function addSymbol(Symbol $symbol): self
    {
        if (!$this->symbols->contains($symbol)) {
            $this->symbols->add($symbol);
            $symbol->addExchange($this);
        }

        return $this;
    }

    public function removeSymbol(Symbol $symbol): self
    {
        if ($this->symbols->removeElement($symbol)) {
            $symbol->removeExchange($this);
        }

        return $this;
    }
}
