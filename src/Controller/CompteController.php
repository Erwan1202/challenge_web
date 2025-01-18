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
        // Créez un nouvel objet CompteBancaire
        $compte = new CompteBancaire();

        // Créez le formulaire sans le champ `numeroDeCompte`
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
            // Génération automatique d'un numéro de compte unique
            $compte->setNumeroDeCompte(random_int(1000000000, 9999999999));

            // Ajout de l'utilisateur connecté comme propriétaire du compte
            $compte->setUtilisateur($this->getUser());

            // Ajout des règles de gestion
            if ($compte->getType() === 'epargne' && $compte->getSolde() < 10) {
                $this->addFlash('error', 'Le solde initial d’un compte épargne doit être d’au moins 10 €.');
                return $this->redirectToRoute('app_add_compte');
            }

            if ($compte->getType() === 'courant') {
                $compte->setDecouvertAutorise(400); // Définir un découvert autorisé pour les comptes courants
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
