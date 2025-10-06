<?php

namespace App\Entity;

use App\Repository\LocationSensorsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationSensorsRepository::class)]
class LocationSensors
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(targetEntity: Locations::class, inversedBy: 'locationSensor', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Locations $location = null;

    #[ORM\ManyToOne(targetEntity: Sensors::class, inversedBy: 'locationSensor', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Sensors $sensor;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getLocation(): ?Locations
    {
        return $this->location;
    }

    public function setLocation(?Locations $location): static
    {
        $this->location = $location;
        return $this;
    }

    public function getSensor(): ?Sensors
    {
        return $this->sensor;
    }

    public function setSensor(?Sensors $sensor): static
    {
        $this->sensor = $sensor;
        return $this;
    }   

    public function jsonSerialize(): array{
        return [
            'id' => $this->id,
            'location' => isset($this->location) ? $this->location->getId() : null,
            'sensor' => isset($this->sensor) ? $this->sensor->getId() : null,
            'active' => $this->active,
            'notes' => $this->notes,
        ];
    }

}
