<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CompteBancaireController extends AbstractController{
    #[Route('/compte/bancaire', name: 'app_compte_bancaire')]
    public function index(): Response
    {
        return $this->render('compte_bancaire/index.html.twig', [
            'controller_name' => 'CompteBancaireController',
        ]);
    }
}
