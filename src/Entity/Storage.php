<?php

namespace App\Entity;

use App\Repository\StorageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StorageRepository::class)]
class Storage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'storages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Building $building = null;

    #[ORM\Column]
    private ?string $laneLetter = null;

    #[ORM\Column]
    private ?int $laneNumber = null;

    /**
     * @var Collection<int, Equipment>
     */
    #[ORM\OneToMany(targetEntity: Equipment::class, mappedBy: 'storage')]
    private Collection $equipment;

    public function __construct()
    {
        $this->equipment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): static
    {
        $this->building = $building;

        return $this;
    }

    public function getLaneLetter(): ?string
    {
        return $this->laneLetter;
    }

    public function setLaneLetter(?string $laneLetter): static
    {
        $this->laneLetter = $laneLetter;

        return $this;
    }

    public function getLaneNumber(): ?int
    {
        return $this->laneNumber;
    }

    public function setLaneNumber(?int $laneNumber): static
    {
        $this->laneNumber = $laneNumber;

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): static
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment->add($equipment);
            $equipment->setStorage($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): static
    {
        if ($this->equipment->removeElement($equipment)) {
            // set the owning side to null (unless already changed)
            if ($equipment->getStorage() === $this) {
                $equipment->setStorage(null);
            }
        }

        return $this;
    }
}
