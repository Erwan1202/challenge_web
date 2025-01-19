<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Utilisateur;
use App\Entity\Transaction;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class CompteBancaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    private ?string $numeroDeCompte = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?float $solde = 0.0;

    #[ORM\Column(nullable: true)]
    private ?float $decouvertAutorise = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'comptes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'compteSource', targetEntity: Transaction::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $transactionsAsSource;

    #[ORM\OneToMany(mappedBy: 'compteDestination', targetEntity: Transaction::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $transactionsAsDestination;

    public function __construct()
    {
        $this->numeroDeCompte = $this->generateNumeroDeCompte();
        $this->transactionsAsSource = new ArrayCollection();
        $this->transactionsAsDestination = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDeCompte(): ?string
    {
        return $this->numeroDeCompte;
    }

    public function setNumeroDeCompte(string $numeroDeCompte): self
    {
        $this->numeroDeCompte = $numeroDeCompte;
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

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;
        return $this;
    }

    public function getDecouvertAutorise(): ?float
    {
        return $this->decouvertAutorise;
    }

    public function setDecouvertAutorise(?float $decouvertAutorise): self
    {
        $this->decouvertAutorise = $decouvertAutorise;
        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getTransactionsAsSource(): Collection
    {
        return $this->transactionsAsSource;
    }

    public function getTransactionsAsDestination(): Collection
    {
        return $this->transactionsAsDestination;
    }

    #[ORM\PrePersist]
    public function prePersistOperations(): void
    {
        $this->generateNumeroDeCompte();
        $this->validateRules();
    }

    private function generateNumeroDeCompte(): string
    {
        return bin2hex(random_bytes(5)); // 10 caractères hexadécimaux
    }

    private function validateRules(): void
    {
        // Validation pour les comptes épargne
        if ($this->type === 'epargne' && $this->solde < 10.0) {
            throw new \InvalidArgumentException('Un compte épargne doit avoir un solde initial d’au moins 10€.');
        }

        // Définir le découvert autorisé pour les comptes courants
        if ($this->type === 'courant') {
            $this->decouvertAutorise = 400.0;
        } else {
            $this->decouvertAutorise = 0;
        }
    }
}
