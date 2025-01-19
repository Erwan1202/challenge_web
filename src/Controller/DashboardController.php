<?php

namespace App\Controller;

use App\Entity\CompteBancaire;
use App\Repository\CompteBancaireRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    // Route pour afficher le tableau de bord
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        CompteBancaireRepository $compteBancaireRepository,
        TransactionRepository $transactionRepository
    ): Response {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Si aucun utilisateur n'est connecté, lancer une exception
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        // Récupérer les comptes bancaires et les transactions de l'utilisateur
        $comptes = $compteBancaireRepository->findBy(['utilisateur' => $user]);
        $transactions = $transactionRepository->findBy([], ['dateHeure' => 'DESC'], 5);

        // Rendre la vue du tableau de bord
        return $this->render('dashboard/index.html.twig', [
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
        

}
