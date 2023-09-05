<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Team;
use App\Entity\User;
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

    /**
     * @return VacationRequest[]
     */
    public function getPendingTeamLeadVacationRequests(User $user): array
    {
        $entityManager = $this->getEntityManager();
        if ($user->isTeamLead()) {
            /** @var TeamRepository $teamRepo */
            $teamRepo = $entityManager->getRepository(Team::class);
            $teams = $teamRepo->getTeamsByLeader($user);
            return array_reduce($teams, function (array $accumulator, Team $team) {
                return array_merge($accumulator, $team->getPendingTeamVacationRequests());
            }, []);
        } else {
            return [];
        }
    }

    /**
     * @return VacationRequest[]
     */
    public function getPendingProjectLeadVacationRequests(User $user): array
    {
        $entityManager = $this->getEntityManager();
        if ($user->isProjectLead()) {
            /** @var ProjectRepository $projectRepo */
            $projectRepo = $entityManager->getRepository(Project::class);
            $projects = $projectRepo->getProjectsByLeader($user);
            return array_reduce($projects, function (array $accumulator, Project $project) {
                return array_merge($accumulator, $project->getPendingProjectVacationRequests());
            }, []);
        } else {
            return [];
        }
    }
}
