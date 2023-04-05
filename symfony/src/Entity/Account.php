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
use App\Repository\AccountRepository;
use App\State\AccountDetailProvider;
use App\State\PostAccountProcessor;
use App\State\SyncProvider;
use App\State\SyncStatusProvider;
use App\State\UpdateAccountProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use function React\Promise\map;

use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
/*
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'exchange', type: 'string')]
#[ORM\DiscriminatorMap([
    BinanceAccount::EXCHANGE => BinanceAccount::class,
])]
*/
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['get:accounts']], security: 'is_granted("ROLE_ADMIN")'),
        new Get(normalizationContext: ['groups' => ['get:accounts', 'get:account']], security: 'object.getUser() == user or is_granted("ROLE_ADMIN")'),
        new Get(
            uriTemplate: '/accounts/{id}/detail',
            provider: AccountDetailProvider::class,
        ),
        new Get(
            uriTemplate: '/accounts/{id}/sync',
            provider: SyncProvider::class,
        ),
        new Get(
            uriTemplate: '/accounts/{id}/update',
            provider: UpdateAccountProvider::class,
        ),
        new Get(
            uriTemplate: '/accounts/{id}/sync_status',
            normalizationContext: ['groups' => ['get:synchro']],
            security: 'is_granted("ROLE_ADMIN")',
            provider: SyncStatusProvider::class,
        ),
        new Post(
            processor: PostAccountProcessor::class,
        ),
    ],
    denormalizationContext: ['groups' => ['create:account', 'update:account']],
)]
/* abstract */
class Account
{
    public const EXCHANGE                          = null;
    public const SYNCHRO_IN_PROGRESS               = 'in_progress';
    public const SYNCHRO_DONE                      = 'done';
    public const SYNCHRO_ERROR                     = 'error';
    public const STEP_TRANSACTION_SYMBOL_OWNED     = 'symbol_owned';
    public const STEP_TRANSACTION_SYMBOL_NOT_OWNED = 'symbol_not_owned';
    public const STEP_FETCH_TRANSFERS              = 'fetch_transfers';

    public const STEPS = [
        self::STEP_FETCH_TRANSFERS,
        self::STEP_TRANSACTION_SYMBOL_OWNED,
        self::STEP_TRANSACTION_SYMBOL_NOT_OWNED,
    ];

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['get:account', 'get:accounts'])]
    private Uuid $id;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['get:account', 'create:account'])]
    private ?Exchange $exchange = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get:account', 'create:account'])]
    private ?string $privateKey = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get:account', 'create:account'])]
    private ?string $publicKey = null;

    #[ORM\ManyToOne(inversedBy: 'account')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['get:account', 'create:account'])]
    #[Assert\NotBlank(groups: ['create:account'])]
    private User $user;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['get:synchro', 'get:account'])]
    private array $synchro = [];

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: Transfer::class, orphanRemoval: true)]
    private Collection $transfers;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: Transaction::class, orphanRemoval: true)]
    private Collection $transactions;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get:account', 'create:account'])]
    private ?string $name = null;

    public function __construct()
    {
        $this->transfers = new ArrayCollection();
        $this->synchro   = [
            'status'    => null,
            'startedAt' => null,
            'step'      => map(self::STEPS, fn ($step) => [$step => null]),
        ];
    }

    public function getExchange(): ?Exchange
    {
        return $this->exchange;
    }

    public function setExchange(Exchange $exchange): self
    {
        $this->exchange = $exchange;

        return $this;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    public function setPrivateKey(string $privateKey): self
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    public function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * @return Collection<int, Transfer>
     */
    public function getTransfers(): Collection
    {
        return $this->transfers;
    }

    public function addTransfer(Transfer $transfer): self
    {
        if (!$this->transfers->contains($transfer)) {
            $this->transfers->add($transfer);
            $transfer->setAccount($this);
        }

        return $this;
    }

    public function removeTransfer(Transfer $transfer): self
    {
        if ($this->transfers->removeElement($transfer)) {
            // set the owning side to null (unless already changed)
            if ($transfer->getAccount() === $this) {
                $transfer->setAccount(null);
            }
        }

        return $this;
    }

    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setAccount($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getAccount() === $this) {
                $transaction->setAccount(null);
            }
        }

        return $this;
    }

    public function getSynchro(): array
    {
        return $this->synchro;
    }

    public function setStartedAt(\DateTime $startedAt): self
    {
        $this->synchro['startedAt'] = $startedAt;

        return $this;
    }

    public function setSynchroStatus(string $status): self
    {
        $this->synchro['status'] = $status;

        return $this;
    }

    public function getSynchroStatus(): ?string
    {
        return $this->synchro['status'] ?? null;
    }

    public function resetSynchro(): self
    {
        $this->synchro = [
            'status'    => self::SYNCHRO_IN_PROGRESS,
            'startedAt' => new \DateTime(),
            'step'      => map(self::STEPS, fn ($step) => [$step => null]),
        ];

        return $this;
    }

    public function setSynchroStep(string $step, \DateTime $startedAt, \DateTime $endedAt, ?array $errors = null): self
    {
        $this->synchro['step'][$step] = [
            'error'     => $errors,
            'startedAt' => $startedAt,
            'endedAt'   => $endedAt,
        ];

        return $this;
    }

    public function checkSynchroStatus(): void
    {
        if (null === $this->synchro['startedAt']) {
            return;
        }

        foreach (self::STEPS as $step) {
            if (!is_array($this->synchro['step']) || false === array_key_exists($step, $this->synchro['step'])) {
                return;
            }
        }

        foreach ($this->synchro['step'] as $step) {
            if (null !== $step['error']) {
                $this->synchro['status'] = self::SYNCHRO_ERROR;

                return;
            }
        }

        $this->synchro['status'] = self::SYNCHRO_DONE;
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
}
