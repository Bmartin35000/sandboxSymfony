<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findOneByTitle($title): ?Recipe
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.title = :val')
            ->setParameter('val', $title)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('r')
            ->getQuery()
            ->getResult()
        ;
    }
}
