<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Utilisateur;

#[ORM\Entity]
class CompteBancaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero_de_compte = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?float $solde = null;

    #[ORM\Column]
    private ?float $decouvertAutorise = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'comptes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDeCompte(): ?int
    {
        return $this->numero_de_compte;
    }

    public function setNumeroDeCompte(int $numero_de_compte): self
    {
        $this->numero_de_compte = $numero_de_compte;
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

    public function setDecouvertAutorise(float $decouvertAutorise): self
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

    #[ORM\PrePersist]
    public function setSoldeInitial(): void
    {
        $this->solde = 10;
    }


    #[ORM\PrePersist]

    public function generateNumeroDeCompte(): void
    {
        $this->numero_de_compte = random_int(1000000000, 9999999999);
    }

}


