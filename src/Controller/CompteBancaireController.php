<?php

namespace App\Controller;

use App\Entity\CompteBancaire;
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
        // Créez un nouvel objet CompteBancaire
        $compte = new CompteBancaire();

        // Utilise le formulaire défini dans CompteFormType
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
                $compte->setDecouvertAutorise(200); // Définir un découvert autorisé de 200 € pour les comptes courants
            }

            // Ajout de l'utilisateur connecté comme propriétaire du compte
            $compte->setUtilisateur($this->getUser());

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
