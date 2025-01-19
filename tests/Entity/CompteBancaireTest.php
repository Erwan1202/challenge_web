<?php

namespace App\Tests\Entity;

use App\Entity\CompteBancaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Teste l'entité CompteBancaire.
 * Cette classe permet de tester les comportements et les règles de validation des comptes bancaires.
 */
class CompteBancaireTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    /**
     * Configuration de l'environnement de test.
     * Initialisation de l'EntityManager pour accéder à la base de données.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$container->get(EntityManagerInterface::class);
    }

    /**
     * Teste la génération du numéro de compte.
     * Vérifie que le numéro de compte généré a bien 10 caractères.
     */
    public function testGenerateNumeroDeCompte()
    {
        // Création d'un compte bancaire avec un solde de 1000
        $compte = new CompteBancaire();
        $compte->setType('courant');
        $compte->setSolde(1000);

        // Récupération du numéro de compte
        $numero = $compte->getNumeroDeCompte();

        // Vérification que le numéro de compte généré n'est pas nul et fait bien 10 caractères
        $this->assertNotNull($numero, "Le numéro de compte ne doit pas être nul.");
        $this->assertEquals(10, strlen($numero), "Le numéro de compte doit avoir une longueur de 10 caractères.");
    }

    /**
     * Teste la validation d'un compte bancaire avec un solde invalide.
     * Vérifie qu'une exception est lancée si le solde est inférieur à 10€ pour un compte épargne.
     */
    public function testCompteBancaireValidation()
    {
        $compte = new CompteBancaire();
        $compte->setType('epargne');
        $compte->setSolde(5); // Solde trop bas

        // Vérification que l'exception est bien lancée
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Un compte épargne doit avoir un solde initial d’au moins 10€.');

        $this->entityManager->persist($compte);
        $this->entityManager->flush(); // Si l'exception n'est pas lancée, le test échoue.
    }
}
