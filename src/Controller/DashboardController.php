<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/user_dashboard.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/dashboard/admin', name: 'app_dashboard_admin')]
    public function admin(): Response
    {
        return $this->render('dashboard/admin_dashboard.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
