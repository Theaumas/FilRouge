<?php

namespace App\Controller;

use App\Entity\Addresse;
use App\Form\AddresseType;
use App\Repository\AddresseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/addresse')]
final class AddresseController extends AbstractController{
    #[Route(name: 'app_addresse_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('addresse/Addresseindex.html.twig');
    }

    #[Route('/new', name: 'app_addresse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $addresse = new Addresse();
        $form = $this->createForm(AddresseType::class, $addresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addresse->setUser($this->getUser());
            // Persist permet de figer les données Info dans le doc perso. 
            $entityManager->persist($addresse);
            // Flush : Permet l'éxecution effective de la requête. 
            $entityManager->flush();

            return $this->redirectToRoute('app_addresse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('addresse/Addressenew.html.twig', [
            'addresse' => $addresse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_addresse_show', methods: ['GET'])]
    public function show(Addresse $addresse): Response
    {
        return $this->render('addresse/AddresseShow.html.twig', [
            'addresse' => $addresse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_addresse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Addresse $addresse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AddresseType::class, $addresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_addresse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('addresse/AddresseEdit.html.twig', [
            'addresse' => $addresse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_addresse_delete', methods: ['POST'])]
    public function delete(Request $request, Addresse $addresse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$addresse->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($addresse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_addresse_index', [], Response::HTTP_SEE_OTHER);
    }
}
