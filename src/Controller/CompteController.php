<?php

namespace App\Controller;

use App\Entity\CompteBancaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

#[Route('/compte')]
class CompteController extends AbstractController
{
    #[Route('/add', name: 'app_add_compte')]
public function addCompte(Request $request, EntityManagerInterface $entityManager): Response
{
    // Récupérer l'utilisateur connecté
    $user = $this->getUser();

    // Vérifier si l'utilisateur a déjà atteint la limite de 5 comptes
    $existingAccounts = $user->getComptes()->count();
    if ($existingAccounts >= 5) {
        // Ajouter un message flash pour informer l'utilisateur
        $this->addFlash('error', 'Vous ne pouvez pas créer plus de 5 comptes bancaires.');
        return $this->redirectToRoute('app_dashboard');
    }

    // Créer un nouvel objet CompteBancaire
    $compte = new CompteBancaire();

    // Créer le formulaire sans le champ `numeroDeCompte`
    $form = $this->createFormBuilder($compte)
        ->add('type', ChoiceType::class, [
            'label' => 'Type de compte',
            'choices' => [
                'Épargne' => 'epargne',
                'Courant' => 'courant',
            ],
        ])
        ->add('solde', NumberType::class, [
            'label' => 'Solde initial',
            'required' => true,
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Générer automatiquement un numéro de compte unique
        $compte->setNumeroDeCompte(random_int(1000000000, 9999999999));

        // Associer l'utilisateur connecté comme propriétaire du compte
        $compte->setUtilisateur($user);

        // Appliquer les règles de gestion
        if ($compte->getType() === 'epargne' && $compte->getSolde() < 10) {
            $this->addFlash('error', 'Le solde initial d’un compte épargne doit être d’au moins 10 €.');
            return $this->redirectToRoute('app_add_compte');
        }

        if ($compte->getType() === 'courant') {
            $compte->setDecouvertAutorise(400); // Définit un découvert autorisé pour les comptes courants à hauteur de 400 €
        }

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
