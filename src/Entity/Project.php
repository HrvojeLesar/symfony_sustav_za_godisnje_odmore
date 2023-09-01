<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'project_lead', nullable: false)]
    private ?User $projectLead = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'project_id', targetEntity: ProjectTeam::class)]
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
}
