<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    #[Route('/utilisateurs', name: 'app_utilisateur')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $utilisateurs = $entityManager->getRepository(Utilisateur::class)->findAll();

        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    private array $roles = [];
    public function GetRoles(): array
    {
        return $this->roles;
        $roles[] = 'ROLE_USER';
        
        return array_unique($roles);
    }

    public function SetRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}


