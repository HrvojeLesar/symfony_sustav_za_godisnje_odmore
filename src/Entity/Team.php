<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PostPersist;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: TeamMember::class, orphanRemoval: true)]
    private Collection $teamMembers;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'team_lead_id', nullable: false)]
    private ?User $teamLead = null;

    public function __construct()
    {
        $this->teamMembers = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, TeamMember>
     */
    public function getTeamMembers(): Collection
    {
        return $this->teamMembers;
    }

    public function addTeamMember(TeamMember $teamMember): static
    {
        if (!$this->teamMembers->contains($teamMember)) {
            $this->teamMembers->add($teamMember);
            $teamMember->setTeam($this);
        }

        return $this;
    }

    public function removeTeamMember(TeamMember $teamMember): static
    {
        if ($this->teamMembers->removeElement($teamMember)) {
            // set the owning side to null (unless already changed)
            if ($teamMember->getTeam() === $this) {
                $teamMember->setTeam(null);
            }
        }

        return $this;
    }

    public function getTeamLead(): ?User
    {
        return $this->teamLead;
    }

    public function setTeamLead(?User $teamLead): static
    {
        $this->teamLead = $teamLead;

        return $this;
    }

    #[PostPersist]
    public function postPersist(PostPersistEventArgs $event): void
    {
        $entityManager = $event->getObjectManager();
        $teamLead = $this->getTeamLead();
        $teamLead->appendRole(Role::TeamLead);
        $entityManager->persist($teamLead);

        $teamMember = new TeamMember();
        $teamMember->setMember($teamLead);
        $teamMember->setTeam($this);
        $entityManager->persist($teamMember);

        $entityManager->flush();
    }

    /**
     * @return VacationRequest[]
     */
    public function getVacationRequests(): array
    {
        /** @var ReadableCollection<User> $teamMembers */
        return $this->getTeamMembers()->map(function (TeamMember $teamMember) { return $teamMember->getMember(); })
            ->reduce(function (array $accumulator, User $user) {
                /** @var ReadableCollection<VacationRequest> $pendingVacationRequests */
                $pendingVacationRequests = $user
                    ->getVacationRequests();
                if ($pendingVacationRequests->count() > 0) {
                    return array_merge($accumulator, $pendingVacationRequests->toArray());
                } else {
                    return $accumulator;
                }
            }, []);
    }

    /**
     * @return VacationRequest[]
     */
    public function getPendingTeamVacationRequests(): array
    {
        return array_filter($this->getVacationRequests(), function (VacationRequest $vacationRequest) { return $vacationRequest->isPendingTeamLeadApproval(); });
    }
}
