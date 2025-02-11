<?php

    namespace App\Controller;

    use App\Entity\Notification;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    #[Route('/notifications')]
    class NotificationController extends AbstractController
    {
        #[Route('/lu/{id}', name: 'app_notification_lu')]
        public function markAsRead(Notification $notification, EntityManagerInterface $entityManager): Response
        {
            if ($notification->getUser() !== $this->getUser()) {
                throw $this->createAccessDeniedException();
            }

            $notification->setIsRead(true);
            $entityManager->flush();

            return $this->redirectToRoute('app_notifications');
        }

        #[Route('/notifications', name: 'app_notifications')]
        public function index(EntityManagerInterface $entityManager): Response
        {
            $user = $this->getUser();
            $notifications = $entityManager->getRepository(Notification::class)->findBy(
                ['user' => $user],
                ['createdAt' => 'DESC']
            );

            return $this->render('notifications/notifications.html.twig', [
                'notifications' => $notifications,
            ]);
        }
}
