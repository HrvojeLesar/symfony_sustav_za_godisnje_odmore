<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PostPersist;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'project_lead_id', nullable: false)]
    private ?User $projectLead = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: ProjectTeam::class)]
    private Collection $projectTeams;

    public function __construct()
    {
        $this->projectTeams = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectLead(): ?User
    {
        return $this->projectLead;
    }

    public function setProjectLead(?User $projectLead): static
    {
        $this->projectLead = $projectLead;

        return $this;
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

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, ProjectTeam>
     */
    public function getProjectTeams(): Collection
    {
        return $this->projectTeams;
    }

    public function addProjectTeam(ProjectTeam $projectTeam): static
    {
        if (!$this->projectTeams->contains($projectTeam)) {
            $this->projectTeams->add($projectTeam);
            $projectTeam->setProject($this);
        }

        return $this;
    }

    public function removeProjectTeam(ProjectTeam $projectTeam): static
    {
        if ($this->projectTeams->removeElement($projectTeam)) {
            // set the owning side to null (unless already changed)
            if ($projectTeam->getProject() === $this) {
                $projectTeam->setProject(null);
            }
        }

        return $this;
    }

    #[PostPersist]
    public function postPersist(PostPersistEventArgs $event): void
    {
        $entityManager = $event->getObjectManager();
        $projectLead = $this->getProjectLead();
        $projectLead->appendRole(Role::ProjectLead);
        $entityManager->persist($projectLead);
        $entityManager->flush();
    }

    /**
     * @return VacationRequest[]
     */
    public function getVacationRequests(): array
    {
        return $this->getProjectTeams()->reduce(function (array $accumulator, ProjectTeam $projectTeam) {
            $pendingVacationRequests = $projectTeam->getTeam()->getVacationRequests();
            if (count($pendingVacationRequests) > 0) {
                return array_merge($accumulator, $pendingVacationRequests);
            } else {
                return $accumulator;
            }
        }, []);
    }

    /**
     * @return VacationRequest[]
     */
    public function getPendingProjectVacationRequests(): array
    {
        return array_filter($this->getVacationRequests(), function (VacationRequest $vacationRequest) { return $vacationRequest->isPendingProjectLeadApproval(); });
    }
}
