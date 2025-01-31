<?php

namespace App\Controller;

use App\Classes\ManageSession;
use App\Classes\Projets;
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

    
}
