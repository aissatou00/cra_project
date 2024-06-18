<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends Personne
{
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $admin_title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $admin_phone = null;

    

    public function getAdminTitle(): ?string
    {
        return $this->admin_title;
    }

    public function setAdminTitle(?string $admin_title): static
    {
        $this->admin_title = $admin_title;

        return $this;
    }

    public function getAdminPhone(): ?string
    {
        return $this->admin_phone;
    }

    public function setAdminPhone(?string $admin_phone): static
    {
        $this->admin_phone = $admin_phone;

        return $this;
    }

    
}
