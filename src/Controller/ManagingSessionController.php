<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\ProjetRepository;
use App\Classes\ManageSession;
use App\Repository\ProjetsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ManagingSessionController extends AbstractController
{
    #[Route('/manage/session', name: 'app_managing_session')]
    public function index(ManageSession $team): Response
    {
        $user = $team->getFull();
        return $this->render('manageSession/team.html.twig', [
            'users' => $user,
        ]);
    }

    #[Route('/manage/session/add/{id}', name: 'app_add_team')]
    public function addUser($id, ManageSession $team): Response
    {
        $team->addUser($id);
        // $team->remove();
        return $this->redirectToRoute('app_managing_session');
        
    }

    #[Route('/projet/{projetId}/add-user/{userId}', name: 'app_projet_add_user')]
    public function addUserToProjet($projetId, $userId, ProjetsRepository $projetRepository, UserRepository $userRepository, EntityManagerInterface $em ):  RedirectResponse 
    {
        $projet = $projetRepository->find($projetId);
        $user = $userRepository->find($userId);

        if (!$projet || !$user) {
            throw $this->createNotFoundException('Projet ou utilisateur introuvable.');
        }

      
        $projet->addUser($user);

        $em->persist($projet);
        $em->flush();

        return $this->redirectToRoute('app_projet_show', ['id' => $projetId]);
    }
}
