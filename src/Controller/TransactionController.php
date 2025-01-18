<?php

namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Transaction;
use App\Entity\CompteBancaire;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
use App\Repository\CompteBancaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


final class TransactionController extends AbstractController
    {
        #[Route('/transaction/transfer', name: 'app_transaction')]
    public function index(TransactionRepository $repository): Response
    {
        $transactions = $repository->findAll();

        return $this->render('transaction/transfer.html.twig', [
            'transactions' => $transactions,
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

            return $this->redirectToRoute('app_transaction');
        }

        return $this->render('transaction/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/transaction/{id}', name: 'app_transaction_show', methods: ['GET'])]
    public function show(Transaction $transaction): Response
    {
        return $this->render('transaction/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    #[Route('/transaction/{id}/edit', name: 'app_transaction_edit')]
    public function edit(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_transaction_show', ['id' => $transaction->getId()]);
        }

        return $this->render('transaction/edit.html.twig', [
            'form' => $form->createView(),
            'transaction' => $transaction,
        ]);
    }

    #[Route('/transaction/{id}', name: 'app_transaction_delete', methods: ['POST'])]
    public function delete(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $transaction->getId(), $request->request->get('_token'))) {
            $entityManager->remove($transaction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_transaction');
    }



    #[Route('/transaction/deposit', name: 'app_deposit')]
    public function deposit(Request $request, EntityManagerInterface $entityManager): Response
    {
    // Créez une nouvelle transaction
    $transaction = new Transaction();

    // Création du formulaire en passant l'utilisateur connecté comme option
    $form = $this->createForm(DepositFormType::class, $transaction, [
        'user' => $this->getUser(), // Passe l'utilisateur connecté au formulaire
    ]);

    $form->handleRequest($request);

    // Traitement du formulaire
    if ($form->isSubmitted() && $form->isValid()) {
        // Validation : vérifier que le montant est positif
        if ($transaction->getMontant() <= 0) {
            $this->addFlash('error', 'Le montant doit être supérieur à 0.');
            return $this->redirectToRoute('app_deposit');
        }

        // Définir le type de transaction comme dépôt
        $transaction->setType(Transaction::TYPE_DEPOSIT);
        $transaction->setDateHeure(new \DateTime());
        $transaction->setStatut(Transaction::STATUS_SUCCESS);

        // Mise à jour du solde du compte source
        $compteSource = $transaction->getCompteSource();
        if ($compteSource) {
            $compteSource->setSolde($compteSource->getSolde() + $transaction->getMontant());
        }

        // Persister dans la base de données
        $entityManager->persist($transaction);
        $entityManager->persist($compteSource);
        $entityManager->flush();

        // Message de succès
        $this->addFlash('success', 'Dépôt effectué avec succès.');
        return $this->redirectToRoute('app_dashboard');
    }

    // Retourne la vue avec le formulaire
    return $this->render('transaction/deposit.html.twig', [
        'form' => $form->createView(),
    ]);
    
    }



    #[Route('/transaction/withdraw', name: 'app_withdraw')]
public function withdraw(Request $request, EntityManagerInterface $entityManager): Response
{
    $transaction = new Transaction();

    $form = $this->createFormBuilder($transaction)
        ->add('compteSource', ChoiceType::class, [
            'label' => 'Compte',
            'choices' => $this->getUser()->getComptes()->toArray(),
            'choice_label' => 'numeroDeCompte',
        ])
        ->add('montant', MoneyType::class, [
            'label' => 'Montant à retirer',
            'currency' => 'EUR',
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $compte = $transaction->getCompteSource();

        // Vérification du solde
        if ($compte->getSolde() >= $transaction->getMontant()) {
            $compte->setSolde($compte->getSolde() - $transaction->getMontant());

            $transaction->setType('withdraw');
            $transaction->setDateHeure(new \DateTime());
            $transaction->setMontant($transaction->getMontant());

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



#[Route('/transaction/transfer', name: 'app_transfer')]
public function transfer(Request $request, EntityManagerInterface $entityManager): Response
{
    $transaction = new Transaction();

    $form = $this->createFormBuilder($transaction)
        ->add('compte_source', ChoiceType::class, [
            'label' => 'Compte source',
            'choices' => $this->getUser()->getComptes()->toArray(),
            'choice_label' => 'numeroDeCompte',
        ])
        ->add('compte_destination', ChoiceType::class, [
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

        // Vérification du solde
        if ($compteSource->getSolde() >= $transaction->getMontant()) {
            $compteSource->setSolde($compteSource->getSolde() - $transaction->getMontant());
            $compteDestination->setSolde($compteDestination->getSolde() + $transaction->getMontant());

            $transaction->setType('transfer');
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
    ]);
}


}