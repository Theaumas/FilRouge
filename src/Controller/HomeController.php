<?php

namespace App\Controller;

// use App\Classes\Mail;
// use Mailjet\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // $email = new Mail();
        //     $content ="welcome.html";
        //     $user = $this->getUser();
        //     if ($user){
        //         $vars = [
        //             'prenom' => $user->getPrenom() . ' ' . $user->getNom(), 
        //             'service' => $user->getService()
        //         ];
        //     }else{
        //         $vars = NULL;
        //     }
        //     $email->send("thomascancre@gmail.com", "Luffy", "Bienvenue", $content, $vars);

        // return $this->render('home/home.html.twig', [
        //     'nom' => 'Projet Manager',
        // ]);

        return $this->render('home/home.html.twig');
    }
}
