<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    //    /**
    //     * @return Notification[] Non lue.  
    //     */
        public function findUnreadNotification($user)
        {
            return $this->createQueryBuilder('n')
                ->andWhere('n.user = :user')
                ->andWhere('n.isRead = false')
                ->setParameter('user', $user)
                ->orderBy('n.createdAt', 'DESC')
                ->setMaxResults(4)
                ->getQuery()
                ->getResult();
        }

        // Return tout les notifications 
        public function findAllNotification($user)
        {
            return $this->createQueryBuilder('n')
                ->andWhere('n.user = :user')
                ->setParameter('user', $user)
                ->orderBy('n.createdAt', 'DESC')
                ->getQuery()
                ->getOneOrNullResult();
        }
}
