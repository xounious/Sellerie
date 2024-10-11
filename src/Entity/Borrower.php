<?php

namespace App\Entity;

use App\Repository\BorrowerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BorrowerRepository::class)]
class Borrower
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'borrowers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BorrowerType $type = null;

    #[ORM\OneToOne(mappedBy: 'borrower', cascade: ['persist', 'remove'])]
    private ?Employee $employee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?BorrowerType
    {
        return $this->type;
    }

    public function setType(?BorrowerType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        // unset the owning side of the relation if necessary
        if ($employee === null && $this->employee !== null) {
            $this->employee->setBorrower(null);
        }

        // set the owning side of the relation if necessary
        if ($employee !== null && $employee->getBorrower() !== $this) {
            $employee->setBorrower($this);
        }

        $this->employee = $employee;

        return $this;
    }
}
