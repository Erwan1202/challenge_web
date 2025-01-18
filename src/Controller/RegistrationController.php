<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Redirection des utilisateurs déjà connectés
        if ($this->getUser()) {
            $roles = $this->getUser()->getRoles();

            if (in_array('ROLE_ADMIN', $roles, true)) {
                return $this->redirectToRoute('admin_dashboard'); // Redirection vers le tableau de bord de l'admin
            } else {
                return $this->redirectToRoute('app_dashboard'); // Redirection vers le tableau de bord de l'utilisateur
            }
        }

        $user = new Utilisateur();

        // Utilise le formulaire défini dans RegistrationFormType
        $form = $this->createForm(RegistrationFormType::class, $user);

        // Gère la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();

            // Vérifie si l'email existe déjà
            $existingUser = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $form->get('email')->addError(new \Symfony\Component\Form\FormError('Cette adresse email est déjà utilisée.'));
            } else {
                // Encode le mot de passe
                $plainPassword = $form->get('plainPassword')->getData();
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

                // Ajoute le rôle par défaut
                $user->setRoles(['ROLE_CLIENT']);

                // Sauvegarde dans la base de données
                $entityManager->persist($user);
                $entityManager->flush();

                // Ajoute un message flash
                $this->addFlash('success', 'Inscription réussie !');

                // Redirige vers la page d'accueil
                return $this->redirectToRoute('app_home');
            }
        }

        // Affiche le formulaire dans la vue
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
