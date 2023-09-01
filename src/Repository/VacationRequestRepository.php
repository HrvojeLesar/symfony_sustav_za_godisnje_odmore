<?php

namespace App\Repository;

use App\Entity\VacationRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VacationRequest>
 *
 * @method VacationRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method VacationRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method VacationRequest[]    findAll()
 * @method VacationRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VacationRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VacationRequest::class);
    }
}
