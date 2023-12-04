<?php
// src/Repository/ProduitRepository.php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Produit; // Import any service you want to inject

class ProduitRepository extends ServiceEntityRepository
{
   

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
       
    }

    public function save(Produit $produit): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($produit);
        $entityManager->flush();
    }

    public function remove(Produit $produit): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($produit);
        $entityManager->flush();
    }
}

?>
