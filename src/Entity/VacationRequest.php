<?php

namespace App\Entity;

use App\Repository\VacationRequestRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use App\Validator\VacationRequest as VacationRequestValidator;
use JsonSerializable;

#[ORM\Entity(repositoryClass: VacationRequestRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[VacationRequestValidator]
class VacationRequest implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'vacationRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(name: 'from_date', type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fromDate = null;

    #[ORM\Column(name: 'to_date', type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $toDate = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne]
    private ?User $approvedByTeamLead = null;

    #[ORM\ManyToOne]
    private ?User $approvedByProjectLead = null;

    #[ORM\Column(name: 'is_approved_by_team_lead', nullable: true)]
    private ?bool $isApprovedByTeamLead = null;

    #[ORM\Column(name: 'is_approved_by_project_lead', nullable: true)]
    private ?bool $isApprovedByProjectLead = null;

    #[ORM\Column(name: 'approval_status_team_updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $approvalStatusTeamUpdatedAt = null;

    #[ORM\Column(name: 'approval_status_project_updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $approvalStatusProjectUpdatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'vacationRequests')]
    #[ORM\JoinColumn(name: 'annual_vacation_id', nullable: false)]
    private ?AnnualVacation $annualVacation = null;

    #[ORM\Column]
    private ?int $daysRequested = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getFromDate(): ?\DateTimeInterface
    {
        return $this->fromDate;
    }

    public function setFromDate(\DateTimeInterface $fromDate): static
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getToDate(): ?\DateTimeInterface
    {
        return $this->toDate;
    }

    public function setToDate(\DateTimeInterface $toDate): static
    {
        $this->toDate = $toDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    protected function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    protected function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
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

    public function getIsApprovedByTeamLead(): ?bool
    {
        return $this->isApprovedByTeamLead;
    }

    public function setIsApprovedByTeamLead(?bool $isApprovedByTeamLead): static
    {
        $this->isApprovedByTeamLead = $isApprovedByTeamLead;

        return $this;
    }

    public function getIsApprovedByProjectLead(): ?bool
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

    #[PrePersist]
    public function prePersist(): void
    {
        $this->setCreatedAt(new DateTime("now"));
        $this->setDaysRequested(
            intval(
                $this->getFromDate()->diff($this->getToDate())->format('%a')
            )
        );
    }

    #[PreUpdate]
    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $this->setUpdatedAt(new DateTime("now"));
        if ($event->hasChangedField('approvedByTeamLead') || $event->hasChangedField('isApprovedByTeamLead')) {
            $this->setApprovalStatusTeamUpdatedAt(new DateTime("now"));
        }
        if ($event->hasChangedField('approvedByProjectLead') || $event->hasChangedField('isApprovedByProjectLead')) {
            $this->setApprovalStatusProjectUpdatedAt(new DateTime("now"));
        }
    }

    public function getStatus(): string
    {
        return match (true) {
            $this->isApprovedByTeamLead === false || $this->isApprovedByProjectLead === false => VacationRequestStatus::Rejected,
            $this->isApprovedByTeamLead === true && $this->isApprovedByProjectLead === true => VacationRequestStatus::Approved,
            is_null($this->isApprovedByTeamLead) && $this->isApprovedByProjectLead === true => VacationRequestStatus::PendingTeamLead,
            $this->isApprovedByTeamLead === true && is_null($this->isApprovedByProjectLead) => VacationRequestStatus::PendingProjectLead,
            is_null($this->isApprovedByTeamLead) && is_null($this->isApprovedByProjectLead) => VacationRequestStatus::Pending,
        };
    }

    public function getAnnualVacation(): ?AnnualVacation
    {
        return $this->annualVacation;
    }

    public function setAnnualVacation(?AnnualVacation $annualVacation): static
    {
        $this->annualVacation = $annualVacation;

        return $this;
    }

    public function getDaysRequested(): ?int
    {
        return $this->daysRequested;
    }

    public function setDaysRequested(int $daysRequested): static
    {
        $this->daysRequested = $daysRequested;

        return $this;
    }

    public function isRemovable(): bool
    {
        return is_null($this->isApprovedByTeamLead) && is_null($this->isApprovedByProjectLead);
    }

    public function isPendingTeamLeadApproval(): bool
    {
        return is_null($this->isApprovedByTeamLead) && (is_null($this->isApprovedByProjectLead) || $this->isApprovedByProjectLead === true);
    }

    public function isPendingProjectLeadApproval(): bool
    {
        return is_null($this->isApprovedByProjectLead) && (is_null($this->isApprovedByTeamLead) || $this->isApprovedByTeamLead === true);
    }

    public function isPendingApproval(): bool
    {
        return $this->isPendingTeamLeadApproval() && $this->isPendingProjectLeadApproval();
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

    public function jsonSerialize(): mixed
    {
        return [
            'from' => $this->getFromDate(),
            'to' => $this->getToDate(),
            'status' => $this->getStatus(),
            'description' => $this->getDescription(),
        ];
    }
}
