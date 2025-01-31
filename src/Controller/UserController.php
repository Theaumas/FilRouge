<?php 

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/indexUser.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $uphi): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) { 

            if ($form->isValid()) {

                // Hasher le mot de passe
                if ($form->get('password')->getData()) {
                    dump('Mise à jour du mot de passe');
                    $user->setPassword(
                        $uphi->hashPassword($user, $form->get('password')->getData())
                    );
                }

                // Gestion de la photo
                $fichier = $form->get('photo')->getData();
                if ($fichier) {
                    dump('Upload de la photo');
                    $dossier = $this->getParameter('kernel.project_dir') . '/public/assets/images/user/';
                    $nomFichier = uniqid() . '.' . $fichier->guessExtension();
                    $fichier->move($dossier, $nomFichier);
                    $user->setPhoto('assets/images/user/' . $nomFichier);
                }

                $entityManager->persist($user);
                $entityManager->flush();
               
                $this->addFlash('success', 'Compte créé avec succès !');
                return $this->redirectToRoute('app_login');
            } else {
                dump($form->getErrors(true));
            }
        }

        return $this->render('user/newUser.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/showUser.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $uphi, UserRepository $userRepository): Response
    {

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

 
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

      
            $newEmail = $form->get('email')->getData();
            if ($newEmail !== $user->getEmail()) {
                $existingUser = $userRepository->findOneBy(['email' => $newEmail]);

                if ($existingUser && $existingUser->getId() !== $user->getId()) {
                    $this->addFlash('error', 'L\'email est déjà utilisé par un autre compte.');
                    return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()]);
                }
            }

     
            $password = $form->get('password')->getData();
            if ($password) {
                $hashedPassword = $uphi->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
            } else {
                $this->addFlash('info', 'Le mot de passe n\'a pas été modifié.');
            }

        
            $fichier = $form['photo']->getData();
            if ($fichier) {
                $chemin = "assets/img/user";
                $nomFichier = uniqid() . '.' . $fichier->guessExtension();
                $fichier->move($chemin, $nomFichier);
                $user->setPhoto($chemin . "/" . $nomFichier);
            }

    
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a bien été mis à jour.');

            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
        }

    
        if ($form->isSubmitted() && !$form->isValid()) {
            dump($form->getErrors(true));
        }

        return $this->render('user/editUser.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    
    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
