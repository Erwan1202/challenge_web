<?php

namespace App\Tests\Repository;

use App\Repository\CompteBancaireRepository;
use App\Entity\CompteBancaire;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CompteBancaireRepositoryTest extends KernelTestCase
{
    private $compteBancaireRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->compteBancaireRepository = self::$container->get(CompteBancaireRepository::class);
    }

    public function testFindByUser(): void
    {
        // Simulate a user entity or fetch a real one
        $user = // Add code to fetch or create a user
        $compteBancaires = $this->compteBancaireRepository->findByUser($user);
        
        $this->assertCount(2, $compteBancaires);
    }

    public function testFindOneByAccountNumber(): void
    {
        $account = $this->compteBancaireRepository->findOneByAccountNumber('123456789');
        
        $this->assertNotNull($account);
        $this->assertEquals('123456789', $account->getNumero());
    }
}
