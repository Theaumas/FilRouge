<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Form\ForgetPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

// #[Route('/profil')]
class ForgetPasswordController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    #[Route('/forget/password', name: 'app_forget_password')]
    public function forget(Request $request, UserRepository $userRepository): Response
    {
        //Formulaire 
        $form = $this->createForm(ForgetPasswordType::class);
        $form->handleRequest($request);

        // Traitement du formulaire
        if($form->isSubmitted() && $form->isValid()){
            //    Si email existe ( stock la valeur de l'email)
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneByEmail($email);
            // dd($user);
            $this->addFlash('success', 'Si votre adresse mail existe, vous allez recevoir un mail pour procéder a la réinitialisation de votre mot de passe');
            if($user)
            {
                //  Sert a gérer le token de vérification en format Bit numérique aléatoire
                $randomBytes = random_bytes(15);
                $token = bin2hex($randomBytes);
                $user->setToken($token);
                //Création de la date d'expiration du token de réinitialisation
                $date = new DateTime();
                $date->modify('+15 minutes');
                $user->setTokenExpiredAt($date);

                $this->em->flush();

                // générer l'url qui sera envoyé dans l'email
                $url = $this->generateUrl(
                    'app_reset_password', 
                    ['token' => $token],
                    // Mis en place d'une URL absolue qui ne change pas 
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                $email = new Mail();
                $name = $user->getPrenom() . ' ' . $user->getNom();
                $subject = "Réinitialisation de votre mot de passe";
                $content = "forgetPassword.html";
                $vars = [
                    // Correpond au lien qui validera le changement
                    'link' => $url,
                    // Correspond a la signature 
                    'team' => 'Appli'
                ];
                $email->send($user->getEmail(),$name, $subject, $content, $vars);
            }
            return $this->redirectToRoute('app_forget_password', [], Response::HTTP_SEE_OTHER);
        };
        return $this->render('forget_password/forgetPassword.html.twig', [
            'forgetPassword' => $form,
        ]);
    }
    
    #[Route('/password/reset/{token}', name: 'app_reset_password')]
    public function reset($token, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $uphi): Response
    {
        if(!$token){
            return $this->redirectToRoute('app_forget_password');
        }
        $user = $userRepository->findOneByToken($token);
        //Vérifier la date d'expiration
        $now = new DateTime();
        if(!$user || $now > $user->getTokenExpiredAt()){
            return $this->redirectToRoute('app_forget_password');
        }

        //Formulaire 
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        //Traitement du formulaire 
        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();
            $mdp = $user->getPassword();
            $mdp = $uphi->hashPassword($user, $mdp);
            //remettre le mdp fans l'objet user
            $user->setPassword($mdp);
            $user->setToken(null);
            $user->setTokenExpiredAt(null);
            $this->em->flush();
            $this->addFlash(
                'success', 
                'Le mot de passe a bien été modifié. Tentez de vous reconnecter.'
            );
            return $this->redirectToRoute('app_login');
        }

        return $this->render('forget_password/resetPassword.html.twig', [
            'resetPassword' => $form,
         ]);
    }
}
