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

    #[ORM\Column(name: 'numero_de_compte', type: 'integer', unique: true)]
    private $numeroDeCompte;

    #[ORM\Column(type: 'string', length: 50)]
    private $type; // 'Courant' ou 'Epargne'

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private $solde;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $decouvertAutorise = 200.00; // Seulement pour les comptes Courant

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'comptesBancaires')]
    #[ORM\JoinColumn(nullable: false)]
    private $utilisateur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDeCompte(): ?int
    {
        return $this->numeroDeCompte;
    }

    public function setNumeroDeCompte(int $numeroDeCompte): self
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
}
