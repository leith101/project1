<?php

namespace App\Repository;

use App\Entity\Hedi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hedi>
 *
 * @method Hedi|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hedi|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hedi[]    findAll()
 * @method Hedi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HediRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hedi::class);
    }

//    /**
//     * @return Hedi[] Returns an array of Hedi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Hedi
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
