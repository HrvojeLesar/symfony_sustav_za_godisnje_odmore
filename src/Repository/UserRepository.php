<?php

namespace App\Repository;

use App\Entity\AnnualVacation;
use App\Entity\Project;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\VacationRequest;
use App\Exceptions\EmployeeNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, protected CacheInterface $cache)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User[]
     */
    public function getAllUsersWithNoAnnualVacationRecord(): array
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->leftJoin(AnnualVacation::class, 'ac', Join::WITH, 'ac.user = u.id')
            ->where('ac.user IS NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return User[]
     */
    public function getAllUsersCached(): array
    {
        /** @var User[] $users */
        $users = $this->cache->get('users', function (ItemInterface $item): array {
            $item->expiresAfter(60);
            return $this->findAll();
        });
        foreach ($users as &$user) {
            $user = $this->getEntityManager()->find(User::class, $user->getId());
        }
        return $users;
    }

    /**
     * @throws EmployeeNotFoundException
     */
    public function getUser(int $id): User
    {
        /** @var User|null $user */
        $user = $this->find($id);
        if (is_null($user)) {
            throw new EmployeeNotFoundException();
        }
        return $user;
    }

    /**
     * @throws EmployeeNotFoundException
     * @return Team[]
     */
    public function getUserTeamsCached(int $id): array
    {
        /** @var Team[] $teams */
        $teams = $this->cache->get('user_id_'.$id.'_teams', function (ItemInterface $item) use ($id): array {
            $item->expiresAfter(60);
            $user = $this->getUser($id);
            return $user->getTeams()->toArray();
        });
        return $teams;
    }

    /**
     * @throws EmployeeNotFoundException
     * @return Project[]
     */
    public function getUserProjectsCached(int $id): array
    {
        /** @var Project[] $projects */
        $projects = $this->cache->get('user_id_'.$id.'_projects', function (ItemInterface $item) use ($id): array {
            $item->expiresAfter(60);
            $user = $this->getUser($id);
            return $user->getProjects();
        });
        return $projects;
    }

    /**
     * @throws EmployeeNotFoundException
     * @return VacationRequest[]
     */
    public function getUserVacationRequestsCached(int $id): array
    {
        /** @var VacationRequest[] $projects */
        $vacationRequests = $this->cache->get('user_id_'.$id.'_vacation_requests', function (ItemInterface $item) use ($id): array {
            $item->expiresAfter(60);
            $user = $this->getUser($id);
            return $user->getVacationRequests()->toArray();
        });
        return $vacationRequests;
    }

    public function getUserByEmail(string $email): User|null
    {
        $results = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.email = :email')
            ->andWhere('u.password IS NULL')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();
        if (count($results) !== 1) {
            return null;
        }
        return $results[0];
    }
}
