<?php

namespace App\Controller;

use App\Repository\CompteBancaireRepository;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
        #[Route('/dashboard', name: 'app_dashboard')]
        public function index(
            CompteBancaireRepository $compteBancaireRepository,
            TransactionRepository $transactionRepository
        ): Response {
            $user = $this->getUser();

            if (!$user) {
                throw $this->createAccessDeniedException();
            }

            // Récupérer les comptes bancaires et les transactions de l'utilisateur connecté
            $comptes = $compteBancaireRepository->findBy(['utilisateur' => $user]);
            $transactions = $transactionRepository->findBy([], ['dateHeure' => 'DESC'], 5);

            return $this->render('dashboard/index.html.twig', [
                'user' => $user,
                'comptes' => $comptes,
                'transactions' => $transactions,
            ]);
        }
    }
