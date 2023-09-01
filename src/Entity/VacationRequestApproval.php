<?php

namespace App\Entity;

use App\Repository\VacationRequestApprovalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VacationRequestApprovalRepository::class)]
class VacationRequestApproval
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'approved_by_team_lead_id', nullable: false)]
    private ?User $approvedByTeamLead = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'approved_by_project_lead_id', nullable: false)]
    private ?User $approvedByProjectLead = null;

    #[ORM\Column(name: 'is_approved_by_team_lead', nullable: true)]
    private ?bool $isApprovedByTeamLead = null;

    #[ORM\Column(name: 'is_approved_by_project_lead', nullable: true)]
    private ?bool $isApprovedByProjectLead = null;

    #[ORM\Column(name: 'approval_status_team_updated_at ', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $approvalStatusTeamUpdatedAt = null;

    #[ORM\Column(name: 'approval_status_project_updated_at ', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $approvalStatusProjectUpdatedAt = null;

    #[ORM\Column(name: 'created_at ', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at ', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApprovedByTeamLead(): ?User
    {
        return $this->approvedByTeamLead;
    }

    public function setApprovedByTeamLead(?User $approvedByTeamLead): static
    {
        $this->approvedByTeamLead = $approvedByTeamLead;

        return $this;
    }

    public function getApprovedByProjectLead(): ?User
    {
        return $this->approvedByProjectLead;
    }

    public function setApprovedByProjectLead(?User $approvedByProjectLead): static
    {
        $this->approvedByProjectLead = $approvedByProjectLead;

        return $this;
    }

    public function isIsApprovedByTeamLead(): ?bool
    {
        return $this->isApprovedByTeamLead;
    }

    public function setIsApprovedByTeamLead(?bool $isApprovedByTeamLead): static
    {
        $this->isApprovedByTeamLead = $isApprovedByTeamLead;

        return $this;
    }

    public function isIsApprovedByProjectLead(): ?bool
    {
        return $this->isApprovedByProjectLead;
    }

    public function setIsApprovedByProjectLead(?bool $isApprovedByProjectLead): static
    {
        $this->isApprovedByProjectLead = $isApprovedByProjectLead;

        return $this;
    }

    public function getApprovalStatusTeamUpdatedAt(): ?\DateTimeInterface
    {
        return $this->approvalStatusTeamUpdatedAt;
    }

    public function setApprovalStatusTeamUpdatedAt(?\DateTimeInterface $approvalStatusTeamUpdatedAt): static
    {
        $this->approvalStatusTeamUpdatedAt = $approvalStatusTeamUpdatedAt;

        return $this;
    }

    public function getApprovalStatusProjectUpdatedAt(): ?\DateTimeInterface
    {
        return $this->approvalStatusProjectUpdatedAt;
    }

    public function setApprovalStatusProjectUpdatedAt(?\DateTimeInterface $approvalStatusProjectUpdatedAt): static
    {
        $this->approvalStatusProjectUpdatedAt = $approvalStatusProjectUpdatedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
