<?php

namespace App\Entity;

use App\Exceptions\EntityNotFoundException;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PostPersist;
use JsonSerializable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface, JsonSerializable
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

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Workplace $workplace = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: VacationRequest::class, orphanRemoval: true)]
    private Collection $vacationRequests;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AnnualVacation::class, orphanRemoval: true)]
    private Collection $annualVacations;

    #[ORM\JoinTable(name: 'team_member')]
    #[ORM\JoinColumn(name: 'member_id', nullable: false)]
    #[ORM\ManyToMany(targetEntity: Team::class, orphanRemoval: true)]
    private Collection $teams;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $last_login_date = null;

    public function __construct()
    {
        $this->vacationRequests = new ArrayCollection();
        $this->annualVacations = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

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
    public function postPersist(PostPersistEventArgs $event): void
    {
        $entityManager = $event->getObjectManager();
        $annualVacation = new AnnualVacation();
        $annualVacation->setUser($this);
        $annualVacation->setYear(date('Y'));
        $entityManager->persist($annualVacation);
        $entityManager->flush();
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = Role::User;
        return array_unique($roles);
    }

    /**
     * @param Roles[] $roles
     */
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    public function appendRole(string $role): static
    {
        $this->roles[] = $role;
        $this->roles = array_unique($this->roles);
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, VacationRequest>
     */
    public function getVacationRequests(): Collection
    {
        return $this->vacationRequests;
    }

    public function addVacationRequest(VacationRequest $vacationRequest): static
    {
        if (!$this->vacationRequests->contains($vacationRequest)) {
            $this->vacationRequests->add($vacationRequest);
            $vacationRequest->setUser($this);
        }

        return $this;
    }

    public function removeVacationRequest(VacationRequest $vacationRequest): static
    {
        if ($this->vacationRequests->removeElement($vacationRequest)) {
            // set the owning side to null (unless already changed)
            if ($vacationRequest->getUser() === $this) {
                $vacationRequest->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AnnualVacation>
     */
    public function getAnnualVacations(): Collection
    {
        return $this->annualVacations;
    }

    public function addAnnualVacation(AnnualVacation $annualVacation): static
    {
        if (!$this->annualVacations->contains($annualVacation)) {
            $this->annualVacations->add($annualVacation);
            $annualVacation->setUser($this);
        }

        return $this;
    }

    public function removeAnnualVacation(AnnualVacation $annualVacation): static
    {
        if ($this->annualVacations->removeElement($annualVacation)) {
            // set the owning side to null (unless already changed)
            if ($annualVacation->getUser() === $this) {
                $annualVacation->setUser(null);
            }
        }

        return $this;
    }

    /**
    * @throws NotFoundException
    */
    public function getLatestAnnualVacation(): AnnualVacation
    {
        $annualVacation = $this->getAnnualVacations()
        ->filter(function (AnnualVacation $av) {
            return $av->getYear() === date('Y');
        })
        ->first();

        if (is_null($annualVacation)) {
            throw new EntityNotFoundException('Annual vacation not found');
        }

        return $annualVacation;
    }

    public function getAvailableVacationDays(): int
    {

        return $this->getLatestAnnualVacation()->availableVacationDays();
    }

    public function isProjectLead(): bool
    {
        return in_array(Role::ProjectLead, $this->getRoles());
    }

    public function isTeamLead(): bool
    {
        return in_array(Role::TeamLead, $this->getRoles());
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    /**
     * @return Project[]
     */
    public function getProjects(): array
    {
        return $this->getTeams()->reduce(
            function (array $accumulator, Team $team) {
                $projects = $team->getProjects();
                return array_unique(array_merge($accumulator, $projects->toArray()), SORT_REGULAR);
            },
            []
        );
    }

    /**
     * @return Team[]
     */
    public function leaderOfTeams(): array
    {
        if (! $this->isTeamLead()) {
            return [];
        }
        return $this->getTeams()->filter(
            function (Team $team) {
                return $team->getTeamLead()->getId() === $this->getId();
            }
        )->toArray();
    }

    /**
     * @return Project[]
     */
    public function leaderOfProjects(): array
    {
        if (! $this->isProjectLead()) {
            return [];
        }

        return $this->getTeams()->reduce(
            function (array $accumulator, Team $team) {
                $projects = $team->getProjects()->filter(function (Project $project) { return $project->getProjectLead()->getId() === $this->getId(); });
                return array_unique(array_merge($accumulator, $projects->toArray()), SORT_REGULAR);
            },
            []
        );

    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'email' => $this->getEmail(),
            'workplace' => $this->getWorkplace(),
            'roles' => $this->getRoles(),
            'vacation_requests' => $this->getVacationRequests()->toArray(),
            'available_vacation_days' => $this->getAvailableVacationDays(),
        ];
    }

    public static function getCSVHeader(): string
    {
        return 'id,first_name,last_name,email,workplace';
    }

    public function toCSV(): string
    {
        return implode(',', [
            $this->getId(),
            $this->getFirstName(),
            $this->getLastName(),
            $this->getEmail(),
            $this->getWorkplace() ? $this->getWorkplace()->__toString() : '',
        ]);
    }

    public function getLastLoginDate(): ?\DateTimeInterface
    {
        return $this->last_login_date;
    }

    public function setLastLoginDate(?\DateTimeInterface $last_login_date): static
    {
        $this->last_login_date = $last_login_date;

        return $this;
    }
}
