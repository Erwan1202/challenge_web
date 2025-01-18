<?php

namespace App\Controller;

use App\Repository\CompteBancaireRepository;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    // Define a route for the dashboard page
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        CompteBancaireRepository $compteBancaireRepository,
        TransactionRepository $transactionRepository
    ): Response {
        // Get the currently logged-in user
        $user = $this->getUser();

        // If no user is logged in, throw an access denied exception
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        // Retrieve the bank accounts and transactions of the logged-in user
        $comptes = $compteBancaireRepository->findBy(['utilisateur' => $user]);
        $transactions = $transactionRepository->findBy([], ['dateHeure' => 'DESC'], 5);

        // Render the dashboard view with the user, accounts, and transactions data
        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
            'comptes' => $comptes,
            'transactions' => $transactions,
        ]);
    }
}
