<?php

namespace App\Controller;

use App\Entity\CompteBancaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $form = $this->createFormBuilder($compte)
            ->add('numeroDeCompte', TextType::class, [
                'label' => 'Numéro de compte',
                'required' => true,
            ])
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
