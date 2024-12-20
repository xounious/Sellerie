<?php

namespace App\Entity;

use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $size = null;

    #[ORM\ManyToOne(inversedBy: 'equipment')]
    private ?Storage $storage = null;

    #[ORM\ManyToOne(inversedBy: 'equipment')]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'equipment')]
    private ?EquipmentCategory $category = null;

    #[ORM\Column]
    private ?int $stockQuantity = null;

    /**
     * @var Collection<int, Loan>
     */
    #[ORM\OneToMany(targetEntity: Loan::class, mappedBy: 'equipment', orphanRemoval: true)]
    private Collection $loans;

    /**
     * @var Collection<int, EquipmentLogs>
     */
    #[ORM\OneToMany(targetEntity: EquipmentLogs::class, mappedBy: 'equipment', orphanRemoval: true)]
    private Collection $equipmentLogs;

    public function __construct()
    {
        $this->loans = new ArrayCollection();
        $this->equipmentLogs = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getStorage(): ?Storage
    {
        return $this->storage;
    }

    public function setStorage(?Storage $storage): static
    {
        $this->storage = $storage;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCategory(): ?EquipmentCategory
    {
        return $this->category;
    }

    public function setCategory(?EquipmentCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getStockQuantity(): ?int
    {
        return $this->stockQuantity;
    }

    public function setStockQuantity(int $stockQuantity): static
    {
        $this->stockQuantity = $stockQuantity;

        return $this;
    }

    /**
     * @return Collection<int, Loan>
     */
    public function getLoans(): Collection
    {
        return $this->loans;
    }

    public function addLoan(Loan $loan): static
    {
        if (!$this->loans->contains($loan)) {
            $this->loans->add($loan);
            $loan->setEquipment($this);
        }

        return $this;
    }

    public function removeLoan(Loan $loan): static
    {
        if ($this->loans->removeElement($loan)) {
            // set the owning side to null (unless already changed)
            if ($loan->getEquipment() === $this) {
                $loan->setEquipment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EquipmentLogs>
     */
    public function getEquipmentLogs(): Collection
    {
        return $this->equipmentLogs;
    }

    public function addEquipmentLog(EquipmentLogs $equipmentLog): static
    {
        if (!$this->equipmentLogs->contains($equipmentLog)) {
            $this->equipmentLogs->add($equipmentLog);
            $equipmentLog->setEquipment($this);
        }

        return $this;
    }

    public function removeEquipmentLog(EquipmentLogs $equipmentLog): static
    {
        if ($this->equipmentLogs->removeElement($equipmentLog)) {
            // set the owning side to null (unless already changed)
            if ($equipmentLog->getEquipment() === $this) {
                $equipmentLog->setEquipment(null);
            }
        }

        return $this;
    }
}
