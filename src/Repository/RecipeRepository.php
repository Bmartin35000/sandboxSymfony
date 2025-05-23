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


    public function findAll(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.title')
            ->getQuery()
            ->getResult()
        ;
    }
}
