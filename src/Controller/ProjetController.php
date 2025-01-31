<?php

namespace App\Controller;

use App\Classes\Search;
use App\Entity\Projets;
use App\Form\ProjetType;
use App\Form\SearchType;
use App\Repository\ProjetsRepository;
use App\Repository\UserRepository;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projets')]
class ProjetController extends AbstractController
{
    #[Route('/', name: 'app_projet_index')]
    public function index(ProjetsRepository $ProjetsRepository): Response
    {
        return $this->render('projet/ProjetsIndex.html.twig', [
           'projets' => $ProjetsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_projet_new', methods:['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
    $projet = new Projets();
    $form = $this->createForm(ProjetType::class, $projet);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user = $this->getUser();
        if ($user) { 
            $projet->addMembre($user); 
        } else {
            $this->addFlash('error', 'Utilisateur non authentifié.');
            return $this->redirectToRoute('app_login');
        }

        $entityManager->persist($projet);
        $entityManager->flush();

        $this->addFlash('success', 'Le projet a été créé avec succès.');
        return $this->redirectToRoute('app_projet_index');
    }

    return $this->render('projet/ProjetsNew.html.twig', [
        'form' => $form->createView(),
    ]);
    }

    #[Route('/{id}/show', name: 'app_projet_show')]
    public function show(Projets $projet): Response
    {
        return $this->render('projet/ProjetsShow.html.twig', [
            'projet' => $projet,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_projet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Projets $projet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Le projet a été modifié avec succès.');
            return $this->redirectToRoute('app_projet_index');
        }

        return $this->render('projet/ProjetsEdit.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_projet_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Projets $projet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $projet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($projet);
            $entityManager->flush();

            $this->addFlash('success', 'Le projet a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_projet_index');
    }

    #[Route('/affecter/{id}', name: 'app_projet_affecter')]
    public function affecterMembres(int $id, ProjetsRepository $projetRepository, UserRepository $userRepository, Request $request): Response
    {

        $projet = $projetRepository->find($id);
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        if (!$projet) {
            throw $this->createNotFoundException('Projet non trouvé.');
        }
    
        $users = $userRepository->findBySearch($search);
    
        return $this->render('projet/affecterProjet.html.twig', [
            'projet' => $projet,
            'users' => $users,
            'f' => $form->createView()
        ]);
    }

}
