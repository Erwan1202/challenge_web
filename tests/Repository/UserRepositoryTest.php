<?php

namespace App\Tests\Repository;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private $userRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->userRepository = self::$container->get(UserRepository::class);
    }

    public function testFindByEmail(): void
    {
        $email = 'testuser@example.com';
        $user = $this->userRepository->findOneByEmail($email);
        
        $this->assertNotNull($user);
        $this->assertEquals($email, $user->getEmail());
    }

    public function testFindAllAdmins(): void
    {
        $admins = $this->userRepository->findAllAdmins();
        
        $this->assertNotEmpty($admins);
        $this->assertTrue(all($admins, fn($user) => $user->hasRole('ROLE_ADMIN')));
    }
}
