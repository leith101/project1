<?php

namespace App\Repository;

use App\Entity\Velo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Velo>
 *
 * @method Velo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Velo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Velo[]    findAll()
 * @method Velo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VeloRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Velo::class);
    }

//    /**
//     * @return Velo[] Returns an array of Velo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Velo
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
