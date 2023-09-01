<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PostPersist;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'first_name', type: Types::TEXT)]
    private ?string $firstName = null;

    #[ORM\Column(name: 'last_name', type: Types::TEXT)]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $email = null;

    #[ORM\Column(name: 'is_admin')]
    private ?bool $isAdmin = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Workplace $workplace = null;

    public function __toString(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): static
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function getWorkplace(): ?Workplace
    {
        return $this->workplace;
    }

    public function setWorkplace(?Workplace $workplace): static
    {
        $this->workplace = $workplace;

        return $this;
    }

    #[PostPersist]
    public function postPersist(PostPersistEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();
        $annualVacation = new AnnualVacation();
        $annualVacation->setUser($this);
        $annualVacation->setYear(date("Y"));
        $entityManager->persist($annualVacation);
        $entityManager->flush();
    }
}
