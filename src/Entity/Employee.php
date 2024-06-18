<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee extends Personne
{
   

    #[ORM\Column(length: 15, nullable: true)]
    #[Assert\NotBlank(message: "Le numéro de mobile ne peut pas être vide.")]
    #[Assert\Regex(
        pattern: '/^\d+$/',
        message: 'Le numéro de mobile ne doit contenir que des chiffres.'
    )]
    private ?int $mobile = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "L'adresse ne peut pas être vide.")]
    #[Assert\Regex(
        pattern: '/^[\w\s-]+$/',
        message: "L'adresse doit contenir des lettres, des chiffres, des espaces et des tirets uniquement."
    )]
    private ?string $address = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthday = null;

    #[ORM\ManyToOne(inversedBy: 'employee')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Department $department = null;


    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Leave::class)]
    private Collection $leaves;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Pointage::class)]
    private Collection $pointages;

 
    public function __construct()
    {
        // Appel du constructeur de la classe parente pour initialiser les collections
        parent::__construct();
        $this->leaves = new ArrayCollection();
        $this->pointages = new ArrayCollection();
    }

    public function getMobile(): ?int
    {
        return $this->mobile;
    }

    public function setMobile(?int $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): static
    {
        $this->department = $department;

        return $this;
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
            $leaf->setEmployee($this);
        }

        return $this;
    }

    public function removeLeaf(Leave $leaf): static
    {
        if ($this->leaves->removeElement($leaf)) {
            // set the owning side to null (unless already changed)
            if ($leaf->getEmployee() === $this) {
                $leaf->setEmployee(null);
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
            $pointage->setEmployee($this);
        }

        return $this;
    }

    public function removePointage(Pointage $pointage): static
    {
        if ($this->pointages->removeElement($pointage)) {
            // set the owning side to null (unless already changed)
            if ($pointage->getEmployee() === $this) {
                $pointage->setEmployee(null);
            }
        }

        return $this;
    }

}
