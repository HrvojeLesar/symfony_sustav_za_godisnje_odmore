<?php

namespace App\Entity;

use App\Repository\AnnualVacationRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

#[ORM\Entity(repositoryClass: AnnualVacationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AnnualVacation
{
    private const MAXIMUMVACATIONDAYS = 20;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'annualVacations')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 4)]
    private ?string $year = null;

    #[ORM\Column(name: 'maximum_vacation_days')]
    private int $maximumVacationDays = self::MAXIMUMVACATIONDAYS;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'annualVacation', targetEntity: VacationRequest::class, orphanRemoval: true)]
    private Collection $vacationRequests;

    public function __construct()
    {
        $this->vacationRequests = new ArrayCollection();
    }

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

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getMaximumVacationDays(): ?int
    {
        return $this->maximumVacationDays;
    }

    public function setMaximumVacationDays(int $maximumVacationDays): static
    {
        $this->maximumVacationDays = $maximumVacationDays;

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

    #[PrePersist]
    public function onCreate(): void
    {
        $this->setCreatedAt(new DateTime("now"));
    }

    #[PreUpdate]
    public function onUpdate(): void
    {
        $this->setUpdatedAt(new DateTime("now"));
    }

    public function availableVacationDays(): int
    {
        $usedDays = $this->getVacationRequests()->reduce(function (int $accumulator, VacationRequest $vacationRequest) {
            if ($vacationRequest->getStatus() === VacationRequestStatus::Rejected) {
                return $accumulator;
            } else {
                return $accumulator + $vacationRequest->getDaysRequested();
            }
        }, 0);
        return $this->maximumVacationDays - $usedDays;
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
            $vacationRequest->setAnnualVacation($this);
        }

        return $this;
    }

    public function removeVacationRequest(VacationRequest $vacationRequest): static
    {
        if ($this->vacationRequests->removeElement($vacationRequest)) {
            // set the owning side to null (unless already changed)
            if ($vacationRequest->getAnnualVacation() === $this) {
                $vacationRequest->setAnnualVacation(null);
            }
        }

        return $this;
    }
}
