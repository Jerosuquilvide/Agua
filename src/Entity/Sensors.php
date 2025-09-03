<?php

namespace App\Entity;

use App\Repository\SensorsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SensorsRepository::class)]
class Sensors
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $manufacturer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $model = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $serial_number = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sensor_type = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $installed_at = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(length: 255)]
    private ?string $notes = null;

    #[ORM\ManyToOne(inversedBy: 'sensor_id')]
    private ?SensorMagnitudes $sensorMagnitudes = null;

    #[ORM\OneToOne(mappedBy: 'sensor_id', cascade: ['persist', 'remove'])]
    private ?Measurements $measurements = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?string $manufacturer): static
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serial_number;
    }

    public function setSerialNumber(?string $serial_number): static
    {
        $this->serial_number = $serial_number;

        return $this;
    }

    public function getSensorType(): ?string
    {
        return $this->sensor_type;
    }

    public function setSensorType(?string $sensor_type): static
    {
        $this->sensor_type = $sensor_type;

        return $this;
    }

    public function getInstalledAt(): ?\DateTimeImmutable
    {
        return $this->installed_at;
    }

    public function setInstalledAt(?\DateTimeImmutable $installed_at): static
    {
        $this->installed_at = $installed_at;

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

    public function setNotes(string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getSensorMagnitudes(): ?SensorMagnitudes
    {
        return $this->sensorMagnitudes;
    }

    public function setSensorMagnitudes(?SensorMagnitudes $sensorMagnitudes): static
    {
        $this->sensorMagnitudes = $sensorMagnitudes;

        return $this;
    }

    public function getMeasurements(): ?Measurements
    {
        return $this->measurements;
    }

    public function setMeasurements(Measurements $measurements): static
    {
        // set the owning side of the relation if necessary
        if ($measurements->getSensorId() !== $this) {
            $measurements->setSensorId($this);
        }

        $this->measurements = $measurements;

        return $this;
    }
}
