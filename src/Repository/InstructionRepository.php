<?php

namespace App\Repository;

use App\Entity\Instruction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Instruction>
 */
class InstructionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Instruction::class);
    }

    /**
     * @return Instruction[] Returns an array of Instruction objects
        */
    public function findByRecipe($id): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.recipe_id = :val')
            ->setParameter('val', $id)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

}
