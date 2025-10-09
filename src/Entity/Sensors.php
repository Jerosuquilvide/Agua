<?php

namespace App\Entity;

use App\Repository\SensorsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(targetEntity: Measurements::class, mappedBy: 'sensor')]
    private ?Collection $measurements = null;

    #[ORM\OneToMany(targetEntity: LocationSensors::class, mappedBy: 'sensor')]
    private ?Collection $locationSensors = null;

    #[ORM\OneToMany(targetEntity: SensorMagnitudes::class, mappedBy: 'sensor')]
    private ?Collection $sensorMagnitudes = null;

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

    public function getMeasurements(): ?Collection
    {
        return $this->measurements;
    }

    public function setMeasurements(?Collection $measurements): static
    {
        $this->measurements = $measurements;

        return $this;
    }

    public function addMeasurement(Measurements $measurement): static
    {
        $this->measurements->add($measurement);

        return $this;
    }

    public function getLocationSensors(): ?Collection
    {
        return $this->locationSensors;
    }

    public function setLocationSensors(?Collection $locationSensors): static
    {
        $this->locationSensors = $locationSensors;

        return $this;
    }

    public function addLocationSensor(LocationSensors $locationSensor): static
    {
        $this->locationSensors->add($locationSensor);

        return $this;
    }

    public function getSensorMagnitudes(): ?Collection
    {
        return $this->sensorMagnitudes;
    }

    public function setSensorMagnitudes(?Collection $sensorMagnitudes): static
    {
        $this->sensorMagnitudes = $sensorMagnitudes;

        return $this;
    }

    public function addSensorMagnitude(SensorMagnitudes $sensorMagnitude): static
    {
        $this->sensorMagnitudes->add($sensorMagnitude);

        return $this;
    }

    public function jsonSerialize(): array{
        return [
            'id' => $this->id,
            'name' => $this->name,
            'manufacturer' => $this->manufacturer,
            'model' => $this->model,
            'serial_number' => $this->serial_number,
            'sensor_type' => $this->sensor_type,
            'installed_at' => $this->installed_at,
            'active' => $this->active,
            'notes' => $this->notes
        ];
    }

}
