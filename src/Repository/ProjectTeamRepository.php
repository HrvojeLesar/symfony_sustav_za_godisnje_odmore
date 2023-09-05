<?php

namespace App\Repository;

use App\Entity\ProjectTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectTeam>
 *
 * @method ProjectTeam|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectTeam|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectTeam[]    findAll()
 * @method ProjectTeam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectTeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectTeam::class);
    }
}
