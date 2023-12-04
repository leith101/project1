<?php

namespace App\Repository;
use Doctrine\ORM\Query;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamation>
 *
 * @method Reclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamation[]    findAll()
 * @method Reclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }
    public function getStats()
    {
        return $this->createQueryBuilder('r')
            ->select('r.categorie_reclamation, COUNT(r.id_reclamation) AS nombreReclamations')
            ->groupBy('r.categorie_reclamation')
            ->getQuery()
            ->getResult();
    }
    public function getStatsForUserByEtat($userId)
    {
        return $this->createQueryBuilder('r')
            ->select('r.etat_reclamation AS etat_reclamation, COUNT(r.id_utilisateur) AS nombreReclamations')
            ->where('r.id_utilisateur = :userId')
            ->setParameter('userId', $userId)
            ->groupBy('r.etat_reclamation')
            ->getQuery()
            ->getResult();
    }
public function findAllQuery(): \Doctrine\ORM\Query
{
    $query = $this->createQueryBuilder('r')
        ->orderBy('r.id_reclamation', 'DESC') // Assurez-vous que le champ utilisé pour le tri est correct
        ->getQuery();

    return $query;
}
public function findByUserIdFilteredAndSorted($userId, $filterBy = null, $sortBy = 'date_reclamation')
{
    $queryBuilder = $this->createQueryBuilder('r')
        ->where('r.id_utilisateur = :userId')
        ->setParameter('userId', $userId);

    // Appliquer le filtre si nécessaire
    if ($filterBy !== null) {
        // Ajouter les conditions de filtre supplémentaires selon vos besoins
        // Par exemple : $queryBuilder->andWhere('r.someField = :filterValue')->setParameter('filterValue', $filterBy);
    }

    // Appliquer le tri
    // Cet exemple trie par défaut sur la date, mais vous pouvez ajuster cela en fonction de vos besoins
    if ($sortBy === 'date_reclamation') {
        $queryBuilder->orderBy('r.date_reclamation', 'DESC');
    } elseif ($sortBy === 'categorie_reclamation') {
        $queryBuilder->orderBy('r.categorie_reclamation', 'ASC');
    } elseif ($sortBy === 'etat_reclamation') {
        $queryBuilder->orderBy('r.etat_reclamation', 'ASC');
    } else {
        // Gérer d'autres options de tri ici si nécessaire
    }

    return $queryBuilder->getQuery()->getResult();
}

    public function save(Reclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function getFilteredAndSortedQuery($filterBy, $sortBy): Query
{
    $queryBuilder = $this->createQueryBuilder('r');

    // Ajout du filtre
    if ($filterBy) {
        $queryBuilder->andWhere('r.categorie_reclamation = :category')
                     ->setParameter('category', $filterBy);
        // Ajoutez d'autres filtres selon vos besoins
    }

    // Ajout du tri
    if ($sortBy === 'date') {
        $queryBuilder->orderBy('r.date_reclamation', 'DESC');
    } elseif ($sortBy === 'category') {
        $queryBuilder->orderBy('r.categorie_reclamation', 'ASC');
    }
    // Ajoutez d'autres options de tri ici

    return $queryBuilder->getQuery();
}



    public function remove(Reclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Reclamation[] Returns an array of Reclamation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reclamation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }



public function orderByObjetReclamation(): array
{
    return $this->createQueryBuilder('r')
        ->orderBy('LOWER(r.objet_reclamation)', 'ASC')
        ->getQuery()
        ->getResult();
}

public function searchReclamation($categorieReclamation)
{
    $query = $this->createQueryBuilder('r')
        ->where('r.categorie_reclamation LIKE :categorieReclamation')
        ->setParameter('categorieReclamation', '%'.$categorieReclamation.'%')
        ->getQuery();

    return $query->getResult();
}





}