<?php

namespace App\Tests\Entity;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Teste l'entité Utilisateur.
 * Cette classe permet de tester les comportements et les règles de validation des utilisateurs.
 */
class UtilisateurTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    /**
     * Configuration de l'environnement de test.
     * Initialisation de l'EntityManager pour l'accès à la base de données.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$container->get(EntityManagerInterface::class);
    }

    /**
     * Teste la création d'un utilisateur valide.
     * Vérifie que l'utilisateur est bien persité et que ses informations sont correctes.
     */
    public function testCreateUtilisateur()
    {
        // Création d'un utilisateur avec des données valides
        $utilisateur = new Utilisateur();
        $utilisateur->setNom('Dupont');
        $utilisateur->setPrenom('Jean');
        $utilisateur->setEmail('jean.dupont@example.com');
        $utilisateur->setPassword('password');
        $utilisateur->setTelephone('0123456789');

        // Persistance de l'utilisateur dans la base de données
        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();

        // Vérification que l'utilisateur a bien été persité
        $this->assertNotNull($utilisateur->getId(), "L'ID de l'utilisateur ne doit pas être nul.");
        $this->assertEquals('Dupont', $utilisateur->getNom(), "Le nom de l'utilisateur doit être 'Dupont'.");
        $this->assertEquals('jean.dupont@example.com', $utilisateur->getEmail(), "L'email de l'utilisateur doit être 'jean.dupont@example.com'.");
    }

    /**
     * Teste un utilisateur avec un email invalide.
     * Vérifie qu'une exception est lancée lorsqu'un utilisateur avec un email incorrect est créé.
     */
    public function testInvalidEmailUtilisateur()
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('invalid-email'); // Email invalide

        // Vérification que l'exception est bien lancée
        $this->expectException(\Symfony\Component\Validator\Exception\ValidatorException::class);
        
        // Essai de persister l'utilisateur avec un email invalide
        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush(); // Si l'exception n'est pas lancée, le test échoue.
    }
}
