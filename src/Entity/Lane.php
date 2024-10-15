<?php

namespace App\Entity;

use App\Repository\LaneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LaneRepository::class)]
class Lane
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $number = null;

    /**
     * @var Collection<int, Storage>
     */
    #[ORM\OneToMany(targetEntity: Storage::class, mappedBy: 'lane')]
    private Collection $storages;

    public function __construct()
    {
        $this->storages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return Collection<int, Storage>
     */
    public function getStorages(): Collection
    {
        return $this->storages;
    }

    public function addStorage(Storage $storage): static
    {
        if (!$this->storages->contains($storage)) {
            $this->storages->add($storage);
            $storage->setLane($this);
        }

        return $this;
    }

    public function removeStorage(Storage $storage): static
    {
        if ($this->storages->removeElement($storage)) {
            // set the owning side to null (unless already changed)
            if ($storage->getLane() === $this) {
                $storage->setLane(null);
            }
        }

        return $this;
    }
}
