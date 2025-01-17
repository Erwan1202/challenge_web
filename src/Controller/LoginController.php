<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà authentifié, rediriger en fonction de son rôle
        if ($this->getUser()) {
            $roles = $this->getUser()->getRoles();
            if (in_array('ROLE_ADMIN', $roles, true)) {
                return $this->redirectToRoute('admin_dashboard'); // Rediriger vers le tableau de bord admin
            } else {
                return $this->redirectToRoute('app_utilisateur'); // Rediriger vers le tableau de bord utilisateur
            }
        }

        // Si l'utilisateur n'est pas authentifié, afficher le formulaire de login
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // La méthode peut être vide car elle est gérée automatiquement par Symfony
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
