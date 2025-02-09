<?php

namespace App\Repository;

use App\Entity\Tache;
use App\Entity\Projets;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tache>
 */
class TacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tache::class);
    }

    public function countByProjet(Projets $projet)
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t)')
            ->where('t.projet = :projet')
            ->setParameter('projet', $projet)
            ->getQuery()
            ->getSingleScalarResult();
    }
        
    public function countTasksForProject(Projets $projet): int
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.projet = :projet')
            ->setParameter('projet', $projet)
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Tache[] Returns an array of Tache objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tache
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
