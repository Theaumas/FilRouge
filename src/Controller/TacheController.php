<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Entity\Projets;
use App\Entity\User;
use App\Form\TacheType;
use Doctrine\ORM\EntityManager;
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
        'form' => $form->createView(),
        'tache' => $tache,
        'projet' => $projet,
    ]);
}


    #[Route('/{projet}/taches', name: 'app_tache_show', methods: ['GET'])]
    public function showTaches(Projets $projet): Response
    {
        return $this->render('tache/TacheShow.html.twig', [
            'projet' => $projet,
            'taches' => $projet->getTaches(),
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

        return $this->redirectToRoute('app_tache_show', ['projet' => $projet->getId(), 'id' => $tache->getId()]);

    }
}
