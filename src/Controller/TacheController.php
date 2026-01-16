<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use App\Repository\TacheRepository; // <-- Import du Repository
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TacheController extends AbstractController
{
    // On injecte le Repository dans la méthode
    #[Route('/taches', name: 'app_tache_index')]
    public function index(TacheRepository $tacheRepository): Response
    {
        return $this->render('tache/index.html.twig', [
            'taches' => $tacheRepository->findAll(), // On récupère toutes les tâches
        ]);
    }

    // ... Le reste de ta méthode new() ne change pas ...
    #[Route('/taches/new', name: 'app_tache_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // ... (Garde ton code qui fonctionne ici) ...
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tache->setStatut('À faire');
            $tache->setAssigne($this->getUser());

            $entityManager->persist($tache);
            $entityManager->flush();

            return $this->redirectToRoute('app_tache_index');
        }

        return $this->render('tache/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}