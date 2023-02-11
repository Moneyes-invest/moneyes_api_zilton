<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Repository\AssetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AssetRepository::class)]
class Asset
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 3, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 3)]
    #[Assert\Regex(pattern: '/^[A-Z]{3}$/')]
    private string $code;

    #[ORM\ManyToMany(targetEntity: Exchange::class, inversedBy: 'assets')]
    private Collection $exchange;

    public function __construct()
    {
        $this->exchange = new ArrayCollection();
    }

    public function getId(): Uuid
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
