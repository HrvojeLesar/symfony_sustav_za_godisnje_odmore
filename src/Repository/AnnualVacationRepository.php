<?php

namespace App\Repository;

use App\Entity\AnnualVacation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnnualVacation>
 *
 * @method AnnualVacation|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnnualVacation|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnnualVacation[]    findAll()
 * @method AnnualVacation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnualVacationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnnualVacation::class);
    }

    public function generateNewYearlyRecords(): void
    {
        $entityManager = $this->getEntityManager();
        /** @var UserRepository $userRepo */
        $userRepo = $entityManager->getRepository(User::class);
        $userRepo->findAll();
        $users = $userRepo->getAllUsersWithNoAnnualVacationRecord();
        $year = date("Y");
        foreach ($users as &$user) {
            $annualVacation = new AnnualVacation();
            $annualVacation->setUser($user);
            $annualVacation->setYear($year);
            $entityManager->persist($annualVacation);
        }

        $entityManager->flush();
    }
}
