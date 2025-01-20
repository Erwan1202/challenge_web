<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\Utilisateur;
use App\Form\DepositFormType;
use App\Form\WithdrawFormType;
use App\Form\TransferFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TransactionController extends AbstractController
{
    #[Route('/transaction/deposit/{userId?}', name: 'app_deposit')]
    public function deposit(?int $userId, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur
        $utilisateur = $this->getUtilisateur($userId, $entityManager);
        if (!$utilisateur) {
            return $this->redirectToRoute('app_dashboard');
        }

        // Créer une transaction de type dépôt
        $transaction = $this->createTransaction(Transaction::TYPE_DEPOSIT, $utilisateur);

        // Créer et traiter le formulaire
        $form = $this->createForm(DepositFormType::class, $transaction, ['user' => $utilisateur]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compteSource = $transaction->getCompteSource();

            // Vérification du montant total pour un dépôt sur un compte épargne
            if ($compteSource->getType() === 'epargne' && ($compteSource->getSolde() + $transaction->getMontant()) > 25000) {
                $this->addFlash('error', 'Le montant total sur votre compte épargne ne doit pas dépasser 25 000€.');
                return $this->redirectToRoute('app_deposit', ['userId' => $userId]);
            }

            // Mise à jour du solde du compte source
            $compteSource->setSolde($compteSource->getSolde() + $transaction->getMontant());
            $entityManager->persist($compteSource);
            $entityManager->persist($transaction);
            $entityManager->flush();

            $this->addFlash('success', 'Dépôt effectué avec succès.');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('transaction/deposit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/transaction/withdraw/{userId?}', name: 'app_withdraw')]
    public function withdraw(?int $userId, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur
        $utilisateur = $this->getUtilisateur($userId, $entityManager);
        if (!$utilisateur) {
            return $this->redirectToRoute('app_dashboard');
        }

        // Créer une transaction de type retrait
        $transaction = $this->createTransaction(Transaction::TYPE_WITHDRAW, $utilisateur);

        // Créer et traiter le formulaire
        $form = $this->createForm(WithdrawFormType::class, $transaction, ['user' => $utilisateur]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compteSource = $transaction->getCompteSource();
            $montantRetrait = $transaction->getMontant();

            // Vérifications spécifiques pour le retrait
            if ($compteSource->getType() === 'courant' && ($compteSource->getSolde() - $montantRetrait) < -400) {
                $this->addFlash('error', 'Le solde ne peut pas descendre en dessous de -400€ sur un compte courant.');
                return $this->redirectToRoute('app_withdraw', ['userId' => $userId]);
            }

            if ($compteSource->getType() === 'epargne' && $compteSource->getSolde() < $montantRetrait) {
                $this->addFlash('error', 'Le solde est insuffisant pour effectuer ce retrait.');
                return $this->redirectToRoute('app_withdraw', ['userId' => $userId]);
            }

            // Mise à jour du solde du compte source
            $compteSource->setSolde($compteSource->getSolde() - $montantRetrait);
            $entityManager->persist($compteSource);
            $entityManager->persist($transaction);
            $entityManager->flush();

            $this->addFlash('success', 'Retrait effectué avec succès.');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('transaction/withdraw.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/transaction/transfer/{userId?}', name: 'app_transfer')]
    public function transfer(?int $userId, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur
        $utilisateur = $this->getUtilisateur($userId, $entityManager);
        if (!$utilisateur) {
            return $this->redirectToRoute('app_dashboard');
        }

        // Créer une transaction de type virement
        $transaction = $this->createTransaction(Transaction::TYPE_TRANSFER, $utilisateur);

        // Créer et traiter le formulaire
        $form = $this->createForm(TransferFormType::class, $transaction, ['user' => $utilisateur]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compteSource = $transaction->getCompteSource();
            $compteDestination = $transaction->getCompteDestination();
            $montant = $transaction->getMontant();

            // Vérification si le compte source est le même que le compte destination
            if ($compteSource === $compteDestination) {
                $this->addFlash('error', 'Vous ne pouvez pas transférer de l\'argent entre le même compte.');
                return $this->redirectToRoute('app_transfer', ['userId' => $userId]);
            }

            // Vérification du montant total sur le compte épargne de destination
            if ($compteDestination->getType() === 'epargne' && ($compteDestination->getSolde() + $montant) > 25000) {
                $this->addFlash('error', 'Le montant total du compte épargne ne peut pas dépasser 25 000€ après le virement.');
                return $this->redirectToRoute('app_transfer', ['userId' => $userId]);
            }

            // Vérification du solde du compte source
            if ($compteSource->getSolde() >= $montant) {
                $compteSource->setSolde($compteSource->getSolde() - $montant);
                $compteDestination->setSolde($compteDestination->getSolde() + $montant);

                $entityManager->persist($compteSource);
                $entityManager->persist($compteDestination);
                $entityManager->persist($transaction);
                $entityManager->flush();

                $this->addFlash('success', 'Virement effectué avec succès.');
                return $this->redirectToRoute('app_dashboard');
            } else {
                $this->addFlash('error', 'Solde insuffisant.');
                return $this->redirectToRoute('app_transfer', ['userId' => $userId]);
            }
        }

        return $this->render('transaction/transfer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Méthode pour récupérer l'utilisateur
    private function getUtilisateur(?int $userId, EntityManagerInterface $entityManager)
    {
        $utilisateur = $userId 
            ? $entityManager->getRepository(Utilisateur::class)->find($userId)
            : $this->getUser();

        if (!$utilisateur) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
        }

        return $utilisateur;
    }

    // Méthode pour créer une transaction
    private function createTransaction(string $type, Utilisateur $utilisateur): Transaction
    {
        $transaction = new Transaction();
        $transaction->setType($type);
        $transaction->setDateHeure(new \DateTime());
        $transaction->setStatut(Transaction::STATUS_SUCCESS);
        return $transaction;
    }
}