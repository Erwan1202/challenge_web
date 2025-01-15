<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * @return Transaction[] Returns an array of Transaction objects filtered by type
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.type = :type')
            ->setParameter('type', $type)
            ->orderBy('t.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Transaction[] Returns an array of Transaction objects for a specific account
     */
    public function findByAccount(int $accountId): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.compteSource', 'cs')
            ->orWhere('cs.id = :accountId')
            ->setParameter('accountId', $accountId)
            ->orderBy('t.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Example method to find transactions within a date range
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.date BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('t.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
