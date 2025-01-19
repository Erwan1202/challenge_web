<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'unique_email', columns: ['email'])]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(name: "mdp_chiffre", type: "text")]
    private ?string $password = null;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    #[Assert\Regex(
        pattern: "/^\+?[0-9]{10,15}$/",
        message: "Le numéro de téléphone doit être valide et contenir entre 10 et 15 chiffres."
    )]

    #[ORM\Column(length: 20, nullable: false, options: ["default" => "0000000000"])]
    private ?string $telephone = null;
    
    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: CompteBancaire::class)]
    private Collection $comptes;

    public function __construct()
    {
        $this->comptes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // Efface les données sensibles si nécessaire.
    }

    public function getUserIdentifier(): string
    {
        return $this->email; // Utilisez l'email comme identifiant unique
    }

    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(CompteBancaire $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCompte(CompteBancaire $compte): self
    {
        if ($this->comptes->removeElement($compte)) {
            if ($compte->getUtilisateur() === $this) {
                $compte->setUtilisateur(null);
            }
        }

        return $this;
    }
}
