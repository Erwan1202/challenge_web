<?php

namespace App\Controller;

use App\Entity\CompteBancaire;
use App\Entity\Transaction;
use App\Entity\Utilisateur;
use App\Repository\CompteBancaireRepository;
use App\Repository\TransactionRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    // Route pour afficher le tableau de bord
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        CompteBancaireRepository $compteBancaireRepository,
        TransactionRepository $transactionRepository,
        UtilisateurRepository $utilisateurRepository // Ajoutez ce repository pour récupérer les utilisateurs
    ): Response {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Si aucun utilisateur n'est connecté, lancer une exception
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        // Vérifier les rôles de l'utilisateur pour rediriger
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            // Si l'utilisateur est admin, afficher le tableau de bord admin
            $users = $utilisateurRepository->findAll(); // Récupérer tous les utilisateurs

            return $this->render('dashboard/admin_dashboard.html.twig', [
                'user' => $user,
                'comptes' => $compteBancaireRepository->findAll(), // Afficher tous les comptes pour un admin
                'transactions' => $transactionRepository->findBy([], ['dateHeure' => 'DESC'], 5),
                'users' => $users, // Passer la variable users à la vue
            ]);
        }

        // Si l'utilisateur est un client (ROLE_CLIENT), afficher le tableau de bord utilisateur
        $comptes = $compteBancaireRepository->findBy(['utilisateur' => $user]);

        // Récupérer les transactions des comptes de l'utilisateur ou les transactions de virement où l'utilisateur est le destinataire
        $transactions = $transactionRepository->createQueryBuilder('t')
            ->where('t.compteSource IN (:comptes) OR t.compteDestination IN (:comptes)')
            ->setParameter('comptes', $comptes)
            ->orderBy('t.dateHeure', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Afficher le tableau de bord utilisateur
        return $this->render('dashboard/user_dashboard.html.twig', [
            'user' => $user,
            'comptes' => $comptes,
            'transactions' => $transactions,
        ]);
    }

    // Route pour supprimer un compte bancaire
    #[Route('/dashboard/delete/{id}', name: 'app_dashboard_delete', methods: ['POST'])]
    public function delete(Request $request, CompteBancaire $compteBancaire, EntityManagerInterface $entityManager): Response
    {
        // Vérifiez le token CSRF
        if ($this->isCsrfTokenValid('delete' . $compteBancaire->getId(), $request->request->get('_token'))) {
            // Supprimez les transactions où ce compte est la source
            foreach ($compteBancaire->getTransactionsAsSource() as $transaction) {
                $entityManager->remove($transaction);
            }
    
            // Supprimez les transactions où ce compte est la destination
            foreach ($compteBancaire->getTransactionsAsDestination() as $transaction) {
                $entityManager->remove($transaction);
            }
    
            // Supprimez le compte bancaire
            $entityManager->remove($compteBancaire);
            $entityManager->flush();
    
            // Redirigez vers le tableau de bord après la suppression
            $this->addFlash('success', 'Le compte bancaire a été supprimé avec succès.');
            return $this->redirectToRoute('app_dashboard');
        }
    
        // Si le token CSRF est invalide, affichez un message d'erreur
        $this->addFlash('error', 'Échec de la suppression du compte bancaire.');
        return $this->redirectToRoute('app_dashboard');
    }

    // Route pour récupérer les informations utilisateur
    #[Route('/dashboard/user/{id}', name: 'app_dashboard_user_info')]
    public function getUserInfo(Utilisateur $utilisateur, CompteBancaireRepository $compteBancaireRepository, TransactionRepository $transactionRepository): JsonResponse
    {
        // Récupérer les comptes bancaires et les transactions de l'utilisateur sélectionné
        $comptes = $compteBancaireRepository->findBy(['utilisateur' => $utilisateur]);
        $transactions = $transactionRepository->createQueryBuilder('t')
            ->where('t.compteSource IN (:comptes) OR t.compteDestination IN (:comptes)')
            ->setParameter('comptes', $comptes)
            ->orderBy('t.dateHeure', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    
        // Retourner les informations sous forme de JSON
        return new JsonResponse([
            'utilisateur' => [
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'email' => $utilisateur->getEmail(),
                'telephone' => $utilisateur->getTelephone(),
            ],
            'comptes' => array_map(function ($compte) {
                return [
                    'id' => $compte->getId(),  // Ajouter l'ID pour un accès facile lors de la suppression
                    'numeroDeCompte' => $compte->getNumeroDeCompte(),
                    'type' => $compte->getType(),
                    'solde' => $compte->getSolde(),
                ];
            }, iterator_to_array($comptes)), // Utilisation de iterator_to_array pour convertir la collection
            'transactions' => array_map(function ($transaction) {
                return [
                    'dateHeure' => $transaction->getDateHeure()->format('d/m/Y H:i'),
                    'type' => $transaction->getType(),
                    'statut' => $transaction->getStatut(),
                    'montant' => $transaction->getMontant(),
                ];
            }, $transactions),
        ]);
    }

    // Route pour effectuer une transaction (Dépôt, Retrait, Virement)
    #[Route('/dashboard/transaction/{action}', name: 'app_dashboard_transaction', methods: ['POST'])]
    public function executeTransaction(Request $request, string $action, EntityManagerInterface $entityManager): JsonResponse
    {
        // Vérifier l'action
        if (!in_array($action, ['deposit', 'withdraw', 'transfer'])) {
            return new JsonResponse(['success' => false, 'message' => 'Action inconnue.'], Response::HTTP_BAD_REQUEST);
        }

        $compteId = $request->request->get('compteId');
        $montant = $request->request->get('montant');
        
        if (!is_numeric($montant) || $montant <= 0) {
            return new JsonResponse(['success' => false, 'message' => 'Montant invalide.'], Response::HTTP_BAD_REQUEST);
        }

        // Récupérer le compte bancaire concerné
        $compte = $entityManager->getRepository(CompteBancaire::class)->find($compteId);
        if (!$compte) {
            return new JsonResponse(['success' => false, 'message' => 'Compte non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        // Effectuer l'action en fonction du type de transaction
        switch ($action) {
            case 'deposit':
                // Ajouter le montant au solde du compte
                $compte->setSolde($compte->getSolde() + $montant);
                break;
            case 'withdraw':
                // Vérifier si le solde est suffisant
                if ($compte->getSolde() < $montant) {
                    return new JsonResponse(['success' => false, 'message' => 'Solde insuffisant.'], Response::HTTP_BAD_REQUEST);
                }
                // Retirer le montant du solde du compte
                $compte->setSolde($compte->getSolde() - $montant);
                break;
            case 'transfer':
                // Logic pour effectuer un virement
                $compteDestinationId = $request->request->get('compteDestinationId');
                $compteDestination = $entityManager->getRepository(CompteBancaire::class)->find($compteDestinationId);
                if (!$compteDestination) {
                    return new JsonResponse(['success' => false, 'message' => 'Compte de destination non trouvé.'], Response::HTTP_NOT_FOUND);
                }
                // Vérifier le solde avant de transférer
                if ($compte->getSolde() < $montant) {
                    return new JsonResponse(['success' => false, 'message' => 'Solde insuffisant pour le virement.'], Response::HTTP_BAD_REQUEST);
                }
                // Effectuer le virement
                $compte->setSolde($compte->getSolde() - $montant);
                $compteDestination->setSolde($compteDestination->getSolde() + $montant);
                break;
        }

        // Créer une nouvelle transaction
        $transaction = new Transaction();
        $transaction->setCompteSource($compte);
        $transaction->setMontant($montant);
        $transaction->setType($action);
        $transaction->setDateHeure(new \DateTime());
        $transaction->setStatut('Réalisée');
        
        // Persister les changements
        $entityManager->persist($transaction);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => ucfirst($action) . ' effectué avec succès.']);
    }
}
