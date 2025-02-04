<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Entity\Projets;
use App\Entity\User;
use App\Form\TacheType;
use App\Repository\UserRepository;
use App\Repository\TacheRepository;
use App\Classes\Search;
use App\Form\SearchType;
use App\Repository\ProjetsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tache')]
class TacheController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/{projet}', name: 'app_tache_index', requirements: ['projet' => '\d+'], methods: ['GET'])]
    public function index(Projets $projet): Response
    {
        return $this->render('tache/TacheIndex.html.twig', [
            'projet' => $projet,
            'taches' => $projet->getTaches(),
        ]);
    }

    #[Route('/{projet}/new', name: 'app_tache_new', requirements: ['projet' => '\d+'], methods: ['GET', 'POST'])]
    public function new(Request $request, Projets $projet): Response
    {
        $user = $this->getUser();
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tache->setProjet($projet);
            $this->entityManager->persist($tache);
            $this->entityManager->flush();

            $this->addFlash('success', 'Tâche ajoutée avec succès.');
            return $this->redirectToRoute('app_tache_index', ['projet' => $projet->getId()]);
        }

        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('tache/TacheNew.html.twig', [
            'form' => $form->createView(),
            'projet' => $projet,
            'users' => $users,
        ]);
    }

    #[Route('/{projet}/{id}/edit', name: 'app_tache_edit')]
    public function edit(Request $request, Tache $tache, Projets $projet, EntityManagerInterface $entityManager): Response
    {   
    $form = $this->createForm(TacheType::class, $tache);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

      
        return $this->redirectToRoute('app_tache_show', [
            'projet' => $projet->getId(),
            'id' => $tache->getId()
        ]);
    }

    return $this->render('tache/TacheEdit.html.twig', [
        'tache' => $tache,
        'projet' => $projet,
        'form' => $form->createView(),
    ]);
    }


    #[Route('/{projet}/tache/{id}', name: 'app_tache_show', methods: ['GET'])]
    public function show(Projets $projet, $id, TacheRepository $tacheRepository): Response
    {
        $tache = $tacheRepository->find($id);

        if (!$tache) {
            throw $this->createNotFoundException('Cette tâche n\'existe pas.');
        }

        return $this->render('tache/TacheShow.html.twig', [
            'projet' => $projet,
            'tache' => $tache,
        ]);
    }



    #[Route('/{projet}/{id}', name: 'app_tache_delete', methods: ['POST'])]
    public function delete(Request $request, Projets $projet, Tache $tache): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tache->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($tache);
            $this->entityManager->flush();

            $this->addFlash('success', 'Tâche supprimée avec succès.');
        }

        return $this->redirectToRoute('app_tache_index', ['projet' => $projet->getId()]);

    }

    #[Route('/affecter/{tacheId}', name: 'app_tache_affecter')]
    public function affecterMembres($tacheId, TacheRepository $tacheRepository, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $tache = $tacheRepository->find($tacheId);
        $projet = $tache->getProjet();

        if (!$tache) {
            throw $this->createNotFoundException('Tâche introuvable.');
        }

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        $users = $userRepository->findBySearch($search);

        return $this->render('tache/affecterTache.html.twig', [
            'projet' => $projet, 
            'tache' => $tache,
            'users' => $users,
            'f' => $form->createView(),
        ]);
    }

    #[Route('/tache/{projetId}/{tacheId}/ajouter/{userId}', name: 'app_tache_ajouter_membre')]
    public function ajouterMembre($projetId, $tacheId, $userId, ProjetsRepository $projetRepository, TacheRepository $tacheRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $projet = $projetRepository->find($projetId);
        $tache = $tacheRepository->find($tacheId);
        $user = $userRepository->find($userId);

        if (!$tache || !$user || !$tache) {
            throw $this->createNotFoundException('Tâche ou utilisateur introuvable.');
        }

        if (!$projet->getMembres()->contains($user)) {
            $this->addFlash('error', 'L\'utilisateur n\'est pas membre du projet.');
            return $this->redirectToRoute('app_projet_show', ['id' => $projetId]);
        }
        
        if (!$tache->getUsers()->contains($user)) {
            $tache->addUser($user);
            $entityManager->persist($tache);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur ajouté à la tâche avec succès.');
        } else {
            $this->addFlash('warning', 'Cet utilisateur est déjà assigné à cette tâche.');
        }

        return $this->redirectToRoute('app_tache_affecter', ['tacheId' => $tacheId, 'projetId' => $projetId]);
    }




    #[Route('/{projetId}/{tacheId}/remove_member/{userId}', name: 'app_tache_remove_membre', methods: ['POST'])]
    public function removeMemberFromTask($projetId, $tacheId, $userId, TacheRepository $tacheRepository, UserRepository $userRepository, EntityManagerInterface $entityManager, ProjetsRepository $projetRepository): Response
    {
        $projet = $projetRepository->find($projetId);
        $tache = $tacheRepository->find($tacheId);
        $user = $userRepository->find($userId);
    
        if (!$projet || !$tache || !$user) {
            throw $this->createNotFoundException('Projet, tâche ou utilisateur introuvable.');
        }
    
        if ($tache->getUsers()->contains($user)) {
            $tache->removeUser($user); 
            $entityManager->persist($tache);
            $entityManager->flush();
    
            $this->addFlash('success', 'L\'utilisateur a été retiré de la tâche.');
        } else {
            $this->addFlash('error', 'L\'utilisateur n\'est pas attribué à cette tâche.');
        }
    
        return $this->redirectToRoute('app_tache_show', ['projet' => $projetId, 'tacheId' => $tacheId, 'id' => $user]);
    }

}
