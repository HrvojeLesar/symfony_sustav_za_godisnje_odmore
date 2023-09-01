<?php

namespace App\Repository;

use App\Entity\Workplace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Workplace>
 *
 * @method Workplace|null find($id, $lockMode = null, $lockVersion = null)
 * @method Workplace|null findOneBy(array $criteria, array $orderBy = null)
 * @method Workplace[]    findAll()
 * @method Workplace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkplaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Workplace::class);
    }
}
