<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Utilisateur>
* @implements PasswordUpgraderInterface<Utilisateur>
 *
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Utilisateur) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function loadUserByIdentifier(string $usernameOrEmail): ?Utilisateur
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT u
                FROM App\Entity\Utilisateur u
                WHERE u.username = :query
                OR u.email = :query'
        )
            ->setParameter('query', $usernameOrEmail)
           ->getOneOrNullResult();
    }


    public function getTotalUsers(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.idUser)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findbyNom(string $nom)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery("SELECT u FROM App\Entity\Utilisateur u WHERE u.username LIKE :nom")
            ->setParameter('nom', '%' . $nom . '%') // Utilisation de LIKE pour des correspondances partielles
        ;
    
        return $query->getResult();
    }

    public function findByUsername(string $username)
{
    $entityManager = $this->getEntityManager();
    $query = $entityManager
        ->createQuery("SELECT u FROM App\Entity\Utilisateur u WHERE u.username LIKE :username")
        ->setParameter('username', '%' . $username . '%')
    ;

    return $query->getResult();
}

public function findByCriteria(array $fields, string $searchTerm)
{
    $entityManager = $this->getEntityManager();
    $queryBuilder = $entityManager->createQueryBuilder();

    $queryBuilder
        ->select('u')
        ->from(Utilisateur::class, 'u');

    foreach ($fields as $field) {
        $queryBuilder
            ->orWhere("u.$field LIKE :searchTerm")
            ->setParameter('searchTerm', '%' . $searchTerm . '%');
    }

    $query = $queryBuilder->getQuery();

    return $query->getResult();
}

//    /**
//     * @return Utilisateur[] Returns an array of Utilisateur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Utilisateur
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
