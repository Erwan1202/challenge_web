<?php

namespace App\Entity;

use App\Repository\CompteBancaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteBancaireRepository::class)]
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

    #[ORM\ManyToOne(inversedBy: 'compteBancaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?utilisateur $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDeCompte(): ?int
    {
        return $this->numero_de_compte;
    }

    public function setNumeroDeCompte(int $numero_de_compte): static
    {
        $this->numero_de_compte = $numero_de_compte;

        return $this;
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

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): static
    {
        $this->solde = $solde;

        return $this;
    }

    public function getDecouvertAutorise(): ?float
    {
        return $this->decouvertAutorise;
    }

    public function setDecouvertAutorise(float $decouvertAutorise): static
    {
        $this->decouvertAutorise = $decouvertAutorise;

        return $this;
    }

    public function getUtilisateur(): ?utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
