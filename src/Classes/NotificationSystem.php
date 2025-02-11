<?php 


    namespace App\Classes;


    use App\Entity\Notification;
    use App\Entity\User;
    use Doctrine\ORM\EntityManagerInterface;


    class NotificationSystem
    {

        private EntityManagerInterface $em;

        public function __construct(EntityManagerInterface $em)
        {
            $this->em = $em;
        }

        public function createNotification(User $user, $message): void
        {
            $notification = new Notification();
            $notification->setUser($user);
            $notification->setMessage($message);

            $this->em->persist($notification);
            $this->em->flush();
        }

    }