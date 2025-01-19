<?php

namespace App\Controller;

use App\Entity\CompteBancaire;
use App\Entity\Utilisateur;
use App\Form\CompteBancaireFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/compte')]
class CompteBancaireController extends AbstractController
{
    #[Route('/add', name: 'app_add_compte')]
    public function addCompte(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        /** @var Utilisateur $user */
        $user = $this->getUser();

        // Vérifier si l'utilisateur a déjà atteint la limite de 5 comptes
        if ($user->getComptes()->count() >= 5) {
            // Ajouter un message flash pour informer l'utilisateur
            $this->addFlash('error', 'Vous ne pouvez pas créer plus de 5 comptes bancaires.');
            return $this->redirectToRoute('app_dashboard');
        }

        // Créez un nouvel objet CompteBancaire
        $compte = new CompteBancaire();

        // Utilise le formulaire défini dans CompteBancaireFormType
        $form = $this->createForm(CompteBancaireFormType::class, $compte);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validation des règles spécifiques au type de compte
            if ($compte->getSolde() < 0) {
                $this->addFlash('error', 'Le solde initial ne peut pas être négatif.');
                return $this->render('compte/add.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            if ($compte->getType() === 'epargne') {
                if ($compte->getSolde() < 10) {
                    $this->addFlash('error', 'Le solde initial d’un compte épargne doit être d’au moins 10 €.');
                    return $this->render('compte/add.html.twig', [
                        'form' => $form->createView(),
                    ]);
                } else if ($compte->getSolde() > 25000) {
                    $this->addFlash('error', 'Le solde d’un compte épargne ne peut pas dépasser 25000 €.');
                    return $this->render('compte/add.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
            }

            if ($compte->getType() === 'courant') {
                $compte->setDecouvertAutorise(400); // Définir un découvert autorisé de 400 € pour les comptes courants
            }

            // Ajout de l'utilisateur connecté comme propriétaire du compte
            $compte->setUtilisateur($user);

            $entityManager->persist($compte);
            $entityManager->flush();

            $this->addFlash('success', 'Compte bancaire ajouté avec succès.');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('compte/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
