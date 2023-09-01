<?php

namespace App\Repository;

use App\Entity\VacationRequestApproval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VacationRequestApproval>
 *
 * @method VacationRequestApproval|null find($id, $lockMode = null, $lockVersion = null)
 * @method VacationRequestApproval|null findOneBy(array $criteria, array $orderBy = null)
 * @method VacationRequestApproval[]    findAll()
 * @method VacationRequestApproval[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VacationRequestApprovalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VacationRequestApproval::class);
    }
}
