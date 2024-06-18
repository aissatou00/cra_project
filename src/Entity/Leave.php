<?php

namespace App\Entity;

use App\Repository\LeaveRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeaveRepository::class)]
#[ORM\Table(name: '`leave`')]
class Leave
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Leave_from = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Leave_to = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Leave_description = null;

    #[ORM\Column]
    private ?int $Leave_status = null;

    #[ORM\ManyToOne(inversedBy: 'leaves')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    #[ORM\ManyToOne(inversedBy: 'leaves')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LeaveType $leavetype = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $rejectionComment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $medicalCertificatePath = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLeaveFrom(): ?\DateTimeInterface
    {
        return $this->Leave_from;
    }

    public function setLeaveFrom(?\DateTimeInterface $Leave_from): static
    {
        $this->Leave_from = $Leave_from;

        return $this;
    }

    public function getLeaveTo(): ?\DateTimeInterface
    {
        return $this->Leave_to;
    }

    public function setLeaveTo(?\DateTimeInterface $Leave_to): static
    {
        $this->Leave_to = $Leave_to;

        return $this;
    }

    public function getLeaveDescription(): ?string
    {
        return $this->Leave_description;
    }

    public function setLeaveDescription(string $Leave_description): static
    {
        $this->Leave_description = $Leave_description;

        return $this;
    }

    public function getLeaveStatus(): ?int
    {
        return $this->Leave_status;
    }

    public function setLeaveStatus(int $Leave_status): static
    {
        $this->Leave_status = $Leave_status;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getLeavetype(): ?LeaveType
    {
        return $this->leavetype;
    }

    public function setLeavetype(?LeaveType $leavetype): static
    {
        $this->leavetype = $leavetype;

        return $this;
    }
    

    // Getter et Setter pour rejectionComment
    public function getRejectionComment(): ?string
    {
        return $this->rejectionComment;
    }

    public function setRejectionComment(?string $rejectionComment): self
    {
        $this->rejectionComment = $rejectionComment;

        return $this;
    }

    //pour le certificat medical
    public function getMedicalCertificatePath(): ?string
    {
        return $this->medicalCertificatePath;
    }

    public function setMedicalCertificatePath(?string $medicalCertificatePath): static
    {
        $this->medicalCertificatePath = $medicalCertificatePath;

        return $this;
    }    

}
