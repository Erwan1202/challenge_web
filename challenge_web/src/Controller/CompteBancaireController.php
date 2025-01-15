<?php

namespace App\Controller;

use App\Entity\CompteBancaire;
use App\Form\CompteBancaireType;
use App\Repository\CompteBancaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CompteBancaireController extends AbstractController
{
    #[Route('/compte/bancaire', name: 'app_compte_bancaire')]
    public function index(CompteBancaireRepository $repository): Response
    {
        $comptes = $repository->findAll();

        return $this->render('compte_bancaire/index.html.twig', [
            'comptes' => $comptes,
        ]);
    }

    #[Route('/compte/bancaire/new', name: 'app_compte_bancaire_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $compte = new CompteBancaire();
        $form = $this->createForm(CompteBancaireType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($compte);
            $entityManager->flush();

            return $this->redirectToRoute('app_compte_bancaire');
        }

        return $this->render('compte_bancaire/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/compte/bancaire/{id}', name: 'app_compte_bancaire_show', methods: ['GET'])]
    public function show(CompteBancaire $compte): Response
    {
        return $this->render('compte_bancaire/show.html.twig', [
            'compte' => $compte,
        ]);
    }

    #[Route('/compte/bancaire/{id}/edit', name: 'app_compte_bancaire_edit')]
    public function edit(Request $request, CompteBancaire $compte, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CompteBancaireType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_compte_bancaire_show', ['id' => $compte->getId()]);
        }

        return $this->render('compte_bancaire/edit.html.twig', [
            'form' => $form->createView(),
            'compte' => $compte,
        ]);
    }

    #[Route('/compte/bancaire/{id}', name: 'app_compte_bancaire_delete', methods: ['POST'])]
    public function delete(Request $request, CompteBancaire $compte, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $compte->getId(), $request->request->get('_token'))) {
            $entityManager->remove($compte);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_compte_bancaire');
    }
}
