<?php

namespace App\Tests\Repository;

use App\Repository\TransactionRepository;
use App\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TransactionRepositoryTest extends KernelTestCase
{
    private $transactionRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->transactionRepository = self::$container->get(TransactionRepository::class);
    }

    public function testFindTransactionsByAccount(): void
    {
        $account = // Simulate an account entity
        $transactions = $this->transactionRepository->findTransactionsByAccount($account);
        
        $this->assertNotEmpty($transactions);
    }

    public function testFindTransactionsByDateRange(): void
    {
        $startDate = new \DateTime('2024-01-01');
        $endDate = new \DateTime('2024-12-31');
        $transactions = $this->transactionRepository->findTransactionsByDateRange($startDate, $endDate);
        
        $this->assertGreaterThan(0, count($transactions));
    }
}
