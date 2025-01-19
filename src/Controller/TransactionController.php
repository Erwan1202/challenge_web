<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\CompteBancaire;
use App\Form\DepositFormType;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


final class TransactionController extends AbstractController
{
    #[Route('/transaction/deposit', name: 'app_deposit')]
    public function deposit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transaction = new Transaction();

        $form = $this->createForm(DepositFormType::class, $transaction, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validation du montant
            if ($transaction->getMontant() <= 0) {
                $this->addFlash('error', 'Le montant doit être supérieur à 0.');
                return $this->redirectToRoute('app_deposit');
            }

            // Récupérer le compte source
            $compteSource = $transaction->getCompteSource();
            if (!$compteSource) {
                $this->addFlash('error', 'Compte source invalide.');
                return $this->redirectToRoute('app_deposit');
            }

            // Mise à jour du solde
            $compteSource->setSolde($compteSource->getSolde() + $transaction->getMontant());

            // Configuration de la transaction
            $transaction->setType(Transaction::TYPE_DEPOSIT);
            $transaction->setDateHeure(new \DateTime());
            $transaction->setStatut(Transaction::STATUS_SUCCESS);

            $entityManager->persist($transaction);
            $entityManager->flush();

            $this->addFlash('success', 'Dépôt effectué avec succès.');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('transaction/deposit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/transaction/withdraw', name: 'app_withdraw')]
    public function withdraw(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transaction = new Transaction();

        $form = $this->createFormBuilder($transaction)
            ->add('compteSource', EntityType::class, [
                'class' => CompteBancaire::class,
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('cb')
                        ->where('cb.utilisateur = :user')
                        ->setParameter('user', $this->getUser());
                },
                'choice_label' => 'numeroDeCompte',
                'label' => 'Compte',
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant à retirer',
                'currency' => 'EUR',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compte = $transaction->getCompteSource();
            if (!$compte) {
                $this->addFlash('error', 'Compte invalide.');
                return $this->redirectToRoute('app_withdraw');
            }

            // Vérification du solde
            if ($compte->getSolde() >= $transaction->getMontant()) {
                $compte->setSolde($compte->getSolde() - $transaction->getMontant());
                $transaction->setType(Transaction::TYPE_WITHDRAW);
                $transaction->setDateHeure(new \DateTime());
                $transaction->setStatut(Transaction::STATUS_SUCCESS);

                $entityManager->persist($transaction);
                $entityManager->flush();

                $this->addFlash('success', 'Retrait effectué avec succès.');
                return $this->redirectToRoute('app_dashboard');
            } else {
                $this->addFlash('error', 'Solde insuffisant.');
            }
        }

        return $this->render('transaction/withdraw.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/transaction/new', name: 'app_transaction_new')]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $transaction = new Transaction();
    $form = $this->createForm(TransactionType::class, $transaction);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($transaction);
        $entityManager->flush();

        return $this->redirectToRoute('app_transaction_new');
    }

    return $this->render('transaction/new.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/transaction/transfer', name: 'app_transfer')]
public function transfer(Request $request, EntityManagerInterface $entityManager): Response
{
    $transactions = $entityManager->getRepository(Transaction::class)->findAll();

    $transaction = new Transaction();

    $form = $this->createFormBuilder($transaction)
        ->add('compteSource', ChoiceType::class, [
            'label' => 'Compte source',
            'choices' => $this->getUser()->getComptes()->toArray(),
            'choice_label' => 'numeroDeCompte',
        ])
        ->add('compteDestination', ChoiceType::class, [
            'label' => 'Compte destination',
            'choices' => $entityManager->getRepository(CompteBancaire::class)->findAll(),
            'choice_label' => 'numeroDeCompte',
        ])
        ->add('montant', MoneyType::class, [
            'label' => 'Montant à transférer',
            'currency' => 'EUR',
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $compteSource = $transaction->getCompteSource();
        $compteDestination = $transaction->getCompteDestination();

        if ($compteSource->getSolde() >= $transaction->getMontant()) {
            $compteSource->setSolde($compteSource->getSolde() - $transaction->getMontant());
            $compteDestination->setSolde($compteDestination->getSolde() + $transaction->getMontant());

            $transaction->setType(Transaction::TYPE_TRANSFER);
            $transaction->setDateHeure(new \DateTime());
            $transaction->setMontant($transaction->getMontant());

            $entityManager->persist($transaction);
            $entityManager->flush();

            $this->addFlash('success', 'Virement effectué avec succès.');
            return $this->redirectToRoute('app_dashboard');
        } else {
            $this->addFlash('error', 'Solde insuffisant.');
        }
    }

    return $this->render('transaction/transfer.html.twig', [
        'form' => $form->createView(),
        'transactions' => $transactions, // Ajout de la variable manquante
    ]);
}

}
