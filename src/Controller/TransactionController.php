<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\CompteBancaire;
use App\Form\DepositFormType;
use App\Form\TransactionType;
use App\Form\WithdrawFormType;
use App\Form\TransferFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

final class TransactionController extends AbstractController
{
    #[Route('/transaction/deposit', name: 'app_deposit')]
    public function deposit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transaction = new Transaction();
    
        // Définir les valeurs par défaut pour 'type', 'dateHeure' et 'statut'
        $transaction->setType(Transaction::TYPE_DEPOSIT);  // Type de transaction (dépôt)
        $transaction->setDateHeure(new \DateTime());  // Date et heure actuelles
        $transaction->setStatut(Transaction::STATUS_SUCCESS);  // Statut de la transaction (succès)
    
        // Construction du formulaire
        $form = $this->createForm(DepositFormType::class, $transaction, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $compteSource = $transaction->getCompteSource();
    
            if ($compteSource->getType() === 'epargne' && ($compteSource->getSolde() + $transaction->getMontant()) > 25000) {
                $this->addFlash('error', 'Le montant total sur votre compte épargne ne doit pas dépasser 25 000€.'); 
                return $this->redirectToRoute('app_deposit');
            }
    
            // Mise à jour du solde du compte
            $nouveauSolde = $compteSource->getSolde() + $transaction->getMontant();
            $compteSource->setSolde($nouveauSolde);
    
            // Persister les entités
            $entityManager->persist($compteSource);  // Persister le compte mis à jour
            $entityManager->persist($transaction);  // Persister la transaction
            $entityManager->flush();
    
            // Message de succès
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
        
        // Définir les valeurs par défaut pour 'type', 'dateHeure' et 'statut'
        $transaction->setType(Transaction::TYPE_WITHDRAW);  // Utilisation de TYPE_WITHDRAW ici
        $transaction->setDateHeure(new \DateTime());  // Date et heure actuelles
        $transaction->setStatut(Transaction::STATUS_SUCCESS);  // Statut de la transaction (succès)
    
        $form = $this->createForm(WithdrawFormType::class, $transaction);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $compteSource = $transaction->getCompteSource();
            $montantRetrait = $transaction->getMontant();
    
            // Vérification pour un compte courant
            if ($compteSource->getType() === 'courant') {
                if (($compteSource->getSolde() - $montantRetrait) < -400) {
                    $this->addFlash('error', 'Le solde ne peut pas descendre en dessous de -400€ sur un compte courant.');
                    return $this->redirectToRoute('app_withdraw');
                }
            }
    
            // Vérification pour un compte épargne
            if ($compteSource->getType() === 'epargne' && $compteSource->getSolde() < $montantRetrait) {
                $this->addFlash('error', 'Le solde est insuffisant pour effectuer ce retrait.');
                return $this->redirectToRoute('app_withdraw');
            }
    
            // Mise à jour du solde du compte après le retrait
            $compteSource->setSolde($compteSource->getSolde() - $montantRetrait);
    
            // Persister les entités
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

    #[Route('/transaction/new', name: 'app_transaction_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($transaction);
            $entityManager->flush();

            $this->addFlash('success', 'Transaction créée avec succès.');
            return $this->redirectToRoute('app_transaction_new');
        }

        return $this->render('transaction/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/transaction/transfer', name: 'app_transfer')]
    public function transfer(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transaction = new Transaction();
    
        // Définir les valeurs par défaut pour 'type', 'dateHeure' et 'statut'
        $transaction->setType(Transaction::TYPE_TRANSFER);  // Type de la transaction
        $transaction->setDateHeure(new \DateTime());  // Date et heure actuelles
        $transaction->setStatut(Transaction::STATUS_SUCCESS);  // Statut de la transaction (succès)
    
        $form = $this->createForm(TransferFormType::class, $transaction);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $compteSource = $transaction->getCompteSource();
            $compteDestination = $transaction->getCompteDestination();
            $montant = $transaction->getMontant();
    
            if ($compteSource->getSolde() >= $montant) {
                // Mise à jour des soldes
                $compteSource->setSolde($compteSource->getSolde() - $montant);
                $compteDestination->setSolde($compteDestination->getSolde() + $montant);
    
                // Persister la transaction
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
        ]);
    }     
}
