<?php

namespace App\Tests\Entity;

use App\Entity\CompteBancaire;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Teste l'entité Transaction.
 * Cette classe permet de tester les différents types de transactions et leur gestion.
 */
class TransactionTest extends KernelTestCase
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
     * Teste une transaction de dépôt.
     * Vérifie que le solde du compte est correctement mis à jour après un dépôt.
     */
    public function testTransactionDeposit()
    {
        // Création d'un compte bancaire avec un solde initial de 1000€
        $compteSource = new CompteBancaire();
        $compteSource->setType('courant');
        $compteSource->setSolde(1000);

        // Création d'une transaction de dépôt de 200€
        $transaction = new Transaction();
        $transaction->setType(Transaction::TYPE_DEPOSIT);
        $transaction->setMontant(200);
        $transaction->setDateHeure(new \DateTime());
        $transaction->setStatut(Transaction::STATUS_SUCCESS);
        $transaction->setCompteSource($compteSource);

        // Persistance des entités dans la base de données
        $this->entityManager->persist($compteSource);
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        // Vérification que la transaction a bien été enregistrée
        $this->assertNotNull($transaction->getId(), "L'ID de la transaction ne doit pas être nul.");
        
        // Vérification que le solde du compte est bien mis à jour après le dépôt
        $this->assertEquals(1200, $compteSource->getSolde(), "Le solde du compte après dépôt doit être de 1200€.");
    }

    /**
     * Teste une transaction invalide avec un type incorrect.
     * Vérifie que l'exception est bien lancée pour un type de transaction non valide.
     */
    public function testInvalidTransactionType()
    {
        $transaction = new Transaction();
        $transaction->setType('invalid_type'); // Type invalide

        // Vérification que l'exception est bien lancée
        $this->expectException(\Symfony\Component\Validator\Exception\ValidatorException::class);
        
        // Essai de persister la transaction invalide
        $this->entityManager->persist($transaction);
        $this->entityManager->flush(); // Si l'exception n'est pas lancée, le test échoue.
    }
}
