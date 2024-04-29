<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findBySearchTerm($searchTerm)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('LOWER(p.name) LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . strtolower($searchTerm) . '%')
            ->getQuery()
            ->getResult();
    }
}

