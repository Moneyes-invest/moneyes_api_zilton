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
use ApiPlatform\Metadata\GetCollection;
use App\Repository\AssetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            order: ['name' => 'ASC'],
            normalizationContext: ['groups' => ['get:assets']],
        ),
    ],
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
#[ORM\Entity(repositoryClass: AssetRepository::class)]
class Asset
{
    #[ORM\Column(length: 255)]
    #[ORM\Id]
    #[Groups(['get:asset', 'get:assets'])]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['get:asset', 'get:assets'])]
    private string $code;

    #[ORM\ManyToMany(targetEntity: Exchange::class, inversedBy: 'assets')]
    private Collection $exchange;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get:asset', 'get:assets'])]
    private ?string $name = null;

    public function __construct()
    {
        $this->exchange = new ArrayCollection();
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }
}
