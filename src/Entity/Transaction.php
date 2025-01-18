<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAW = 'withdraw';
    public const TYPE_TRANSFER = 'transfer';

    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::TYPE_DEPOSIT, self::TYPE_WITHDRAW, self::TYPE_TRANSFER], message: 'Type de transaction invalide.')]
    private ?string $type = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive(message: 'Le montant doit être supérieur à 0.')]
    private ?float $montant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeInterface $dateHeure = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::STATUS_SUCCESS, self::STATUS_FAILED], message: 'Statut invalide.')]
    private ?string $statut = null;

    #[ORM\ManyToOne(targetEntity: CompteBancaire::class)]
    #[ORM\JoinColumn(nullable: true)] // Peut-être null pour certaines transactions
    private ?CompteBancaire $compteSource = null;

    #[ORM\ManyToOne(targetEntity: CompteBancaire::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?CompteBancaire $compteDestination = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateHeure(): ?\DateTimeInterface
    {
        return $this->dateHeure;
    }

    public function setDateHeure(\DateTimeInterface $dateHeure): static
    {
        $this->dateHeure = $dateHeure;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getCompteSource(): ?CompteBancaire
    {
        return $this->compteSource;
    }

    public function setCompteSource(?CompteBancaire $compteSource): static
    {
        $this->compteSource = $compteSource;

        return $this;
    }

    public function getCompteDestination(): ?CompteBancaire
    {
        return $this->compteDestination;
    }

    public function setCompteDestination(?CompteBancaire $compteDestination): static
    {
        $this->compteDestination = $compteDestination;

        return $this;
    }
}
