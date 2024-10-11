<?php

namespace App\Entity;

use App\Repository\BorrowerTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BorrowerTypeRepository::class)]
class BorrowerType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Borrower>
     */
    #[ORM\OneToMany(targetEntity: Borrower::class, mappedBy: 'type')]
    private Collection $borrowers;

    public function __construct()
    {
        $this->borrowers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Borrower>
     */
    public function getBorrowers(): Collection
    {
        return $this->borrowers;
    }

    public function addBorrower(Borrower $borrower): static
    {
        if (!$this->borrowers->contains($borrower)) {
            $this->borrowers->add($borrower);
            $borrower->setType($this);
        }

        return $this;
    }

    public function removeBorrower(Borrower $borrower): static
    {
        if ($this->borrowers->removeElement($borrower)) {
            // set the owning side to null (unless already changed)
            if ($borrower->getType() === $this) {
                $borrower->setType(null);
            }
        }

        return $this;
    }
}
