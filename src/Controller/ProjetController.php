<?php

namespace App\Controller;

use App\Classes\Search;
use App\Classes\NotificationSystem;
use App\Entity\Projets;
use App\Form\ProjetType;
use App\Form\SearchType;
use App\Repository\ProjetsRepository;
use App\Repository\UserRepository;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projets')]
class ProjetController extends AbstractController
{

    private NotificationSystem $nS;

    public function __construct(NotificationSystem $nS)
    {
        $this->nS = $nS;
    }
    

    #[Route('/', name: 'app_projet_index')]
    public function index(ProjetsRepository $ProjetsRepository, TacheRepository $tacheRepository): Response
    {
        $tasksCount = function($projet) use ($tacheRepository) {
            return $tacheRepository->countTasksForProject($projet);
        };

        return $this->render('projet/ProjetsIndex.html.twig', [
            'projets' => $ProjetsRepository->findAll(),
            'tasksCount'=> $tasksCount,
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
                $projet->setCreator($user);
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
    public function show($id, ProjetsRepository $projetRepository, UserRepository $userRepository, TacheRepository $tacheRepository, Request $request): Response
    {
        $projet = $projetRepository->find($id);

        if (!$projet) {
            throw $this->createNotFoundException('Projet introuvable.');
        }

        $tacheCount = $tacheRepository->countByProjet($projet);

        $user = $this->getUser();

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        $users = $userRepository->findBySearch($search);

        return $this->render('projet/ProjetsShow.html.twig', [
            'projet' => $projet,
            'users' => $users,
            'user' => $user,
            'f' => $form->createView(),
            'tacheCount' => $tacheCount,
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

    #[Route('/affecter/{projetId}', name: 'app_projet_affecter')]
    public function affecterMembres($projetId, ProjetsRepository $projetRepository, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $projet = $projetRepository->find($projetId);
        $user = $this->getUser();  

        if (!$projet || !$user) {
            throw $this->createNotFoundException('Projet ou utilisateur introuvable.');
        }

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
    
        if (!$projet->getMembres()->contains($user)) 
        {
            $projet->addMembre($user);
            $entityManager->persist($projet);
            $entityManager->flush();
        }

        $users = $userRepository->findBySearch($search);

        if (!$projet->getMembres()->contains($user)) {
            $projet->addMembre($user);
            $entityManager->persist($projet);
            $entityManager->flush();
        }

        return $this->render('projet/affecterProjet.html.twig', [
            'projet' => $projet,
            'Membres' => $userRepository->findAll(), 
            'f' => $form->createView(),
        ]);
    }   

    #[Route('/{projetId}/choisir/{userId}', name: 'app_projet_choisir')]
    public function choisir($projetId, $userId, ProjetsRepository $projetRepository, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $projet = $projetRepository->find($projetId);
        $user = $userRepository->find($userId);

        if (!$projet || !$user) {
            throw $this->createNotFoundException('Projet ou utilisateur introuvable.');
        }

        if (!$projet->getMembres()->contains($user)) {
            $projet->addMembre($user);
            $em->persist($projet);
            $em->flush();
        }

        // Envoi Notification d'ajout au projet correspondant à l'id 
        $this->nS->createNotification(
            $user, "Vous avez été ajouté au projet : " . $projet->getNom()
        );

        $this->addFlash('success', 'L\'utilisateur a été ajouté au projet.');

        return $this->redirectToRoute('app_projet_index', ['id' => $projet->getId()]);
    }

    #[Route('/projets/{projetId}/remove_member/{userId}', name: 'app_projet_remove_member', methods: ['POST'])]
    public function removeMember($projetId, $userId, ProjetsRepository $projetRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $projet = $projetRepository->find($projetId);
        $user = $userRepository->find($userId);

        if (!$projet || !$user) {
            throw $this->createNotFoundException('Projet ou utilisateur introuvable.');
        }

        if ($projet->getMembres()->contains($user)) {
            $projet->removeMembre($user);  
            $entityManager->persist($projet);
            $entityManager->flush();

            $this->nS->createNotification(
                $user,
                "Vous avez été retiré du projet : " . $projet->getNom()
            );

            $this->addFlash('success', 'L\'utilisateur a été retiré du projet.');
        } else {
            $this->addFlash('error', 'L\'utilisateur n\'est pas un membre du projet.');
        }

        return $this->redirectToRoute('app_projet_index', ['id' => $projetId]);
    }

}
