<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 */
class UtilisateurRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    
    /**
     * @return Utilisateur[] Returns an array of Utilisateur objects filtered by role
     */
    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.role = :role')
            ->setParameter('role', $role)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Utilisateur[] Returns an array of blocked Utilisateur objects
     */
    public function findBlockedUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.isBlocked = :status')
            ->setParameter('status', true)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Utilisateur|null Returns a Utilisateur object by email
     */
    public function findOneByEmail(string $email): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }


        /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Utilisateur) {
            throw new \InvalidArgumentException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        // Mettre à jour le mot de passe de l'utilisateur
        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }



}