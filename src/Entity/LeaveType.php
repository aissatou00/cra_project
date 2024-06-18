<?php

namespace App\Entity;

use App\Repository\LeaveTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeaveTypeRepository::class)]
class LeaveType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $leave_type = null;

    #[ORM\OneToMany(mappedBy: 'leavetype', targetEntity: Leave::class)]
    private Collection $leaves;

    #[ORM\OneToMany(mappedBy: 'categorieAbsence', targetEntity: Pointage::class)]
    private Collection $pointages;

    public function __construct()
    {
        $this->leaves = new ArrayCollection();
        $this->pointages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLeaveType(): ?string
    {
        return $this->leave_type;
    }

    public function setLeaveType(string $leave_type): static
    {
        $this->leave_type = $leave_type;

        return $this;
    }

    // convertir leavetype en string pour la page leave du tableau de bord d'un employé
    public function __toString(): string
    {
        // Retourne le type de congé pour qu'il puisse être converti en chaîne
        return $this->leave_type;
    }

    /**
     * @return Collection<int, Leave>
     */
    public function getLeaves(): Collection
    {
        return $this->leaves;
    }

    public function addLeaf(Leave $leaf): static
    {
        if (!$this->leaves->contains($leaf)) {
            $this->leaves->add($leaf);
            $leaf->setLeavetype($this);
        }

        return $this;
    }

    public function removeLeaf(Leave $leaf): static
    {
        if ($this->leaves->removeElement($leaf)) {
            // set the owning side to null (unless already changed)
            if ($leaf->getLeavetype() === $this) {
                $leaf->setLeavetype(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pointage>
     */
    public function getPointages(): Collection
    {
        return $this->pointages;
    }

    public function addPointage(Pointage $pointage): static
    {
        if (!$this->pointages->contains($pointage)) {
            $this->pointages->add($pointage);
            $pointage->setCategorieAbsence($this);
        }

        return $this;
    }

    public function removePointage(Pointage $pointage): static
    {
        if ($this->pointages->removeElement($pointage)) {
            // set the owning side to null (unless already changed)
            if ($pointage->getCategorieAbsence() === $this) {
                $pointage->setCategorieAbsence(null);
            }
        }

        return $this;
    }

}
