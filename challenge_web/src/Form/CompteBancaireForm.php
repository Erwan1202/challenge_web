<?php

namespace App\Entity;

use App\Repository\CompteBancaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteBancaireRepository::class)]
class CompteBancaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    private $numero;

    #[ORM\Column(type: 'string', length: 50)]
    private $type; // 'Courant' ou 'Epargne'

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private $solde;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $decouvertMax = 200.00; // Seulement pour les comptes Courant

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'comptesBancaires')]
    #[ORM\JoinColumn(nullable: false)]
    private $proprietaire;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

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

    public function getSolde(): ?string
    {
        return $this->solde;
    }

    public function setSolde(string $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getDecouvertMax(): ?string
    {
        return $this->decouvertMax;
    }

    public function setDecouvertMax(?string $decouvertMax): self
    {
        $this->decouvertMax = $decouvertMax;

        return $this;
    }

    public function getProprietaire(): ?Utilisateur
    {
        return $this->proprietaire;
    }

    public function setProprietaire(?Utilisateur $proprietaire): self
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }
}
