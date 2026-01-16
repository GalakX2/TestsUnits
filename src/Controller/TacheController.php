<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TacheController extends AbstractController
{
    // --- CORRECTION ICI : Changement du nom de la route ---
    // De 'app_tache_index' vers 'app_tache'
    #[Route('/taches', name: 'app_tache')] 
    public function index(TacheRepository $tacheRepository): Response
    {
        return $this->render('tache/index.html.twig', [
            'taches' => $tacheRepository->findAll(),
        ]);
    }

    #[Route('/taches/new', name: 'app_tache_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tache->setStatut('À faire');
            $tache->setAssigne($this->getUser());

            $entityManager->persist($tache);
            $entityManager->flush();

            // --- CORRECTION ICI : Mise à jour de la redirection ---
            return $this->redirectToRoute('app_tache');
        }

        return $this->render('tache/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}