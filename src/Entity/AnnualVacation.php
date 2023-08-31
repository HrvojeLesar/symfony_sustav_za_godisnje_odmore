<?php

namespace App\Entity;

use App\Repository\AnnualVacationRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PreFlush;
use Doctrine\ORM\Mapping\PrePersist;

#[ORM\Entity(repositoryClass: AnnualVacationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AnnualVacation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'annualVacations')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 4)]
    private ?string $year = null;

    #[ORM\Column]
    private ?int $maximum_vacation_days = null;

    #[ORM\Column]
    private ?int $vacation_days_taken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

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
        return $this->maximum_vacation_days;
    }

    public function setMaximumVacationDays(int $maximum_vacation_days): static
    {
        $this->maximum_vacation_days = $maximum_vacation_days;

        return $this;
    }

    public function getVacationDaysTaken(): ?string
    {
        return $this->vacation_days_taken;
    }

    public function setVacationDaysTaken(string $vacation_days_taken): static
    {
        $this->vacation_days_taken = $vacation_days_taken;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    protected function setCreatedAt(?\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    protected function setUpdatedAt(?\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    #[PrePersist]
    public function onCreate(): void
    {
        $this->setCreatedAt(new DateTime("now"));
    }

    #[PreFlush]
    public function onUpdate(): void
    {
        $this->setUpdatedAt(new DateTime("now"));
    }
}
