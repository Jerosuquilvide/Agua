<?php

namespace App\Entity;

use App\Repository\LocationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: LocationsRepository::class)]
class Locations implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type:Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $lat_dd = null;

    #[ORM\Column(type:Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $lon_dd = null;

    #[ORM\Column(type:Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $altitude_m = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(targetEntity: Measurements::class, mappedBy: 'location')]
    private ?Collection $measurements = null;

    #[ORM\OneToMany(targetEntity: LocationSensors::class, mappedBy: 'location')]
    private ?Collection $locationSensor = null;

    #[ORM\OneToMany(targetEntity: LocationMagnitudes::class, mappedBy: 'location')]
    private ?Collection $locationMagnitude = null;

    public function __construct()
    {
        $this->measurements = new ArrayCollection();
        $this->locationSensor = new ArrayCollection();
        $this->locationMagnitude = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLatDd(): ?string
    {
        return $this->lat_dd;
    }

    public function setLatDd(?string $lat_dd): static
    {
        $this->lat_dd = $lat_dd;

        return $this;
    }

    public function getLonDd(): ?string
    {
        return $this->lon_dd;
    }

    public function setLonDd(string $lon_dd): static
    {
        $this->lon_dd = $lon_dd;

        return $this;
    }

    public function getAltitudeM(): ?string
    {
        return $this->altitude_m;
    }

    public function setAltitudeM(?string $altitude_m): static
    {
        $this->altitude_m = $altitude_m;

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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

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

    public function getLocationSensor(): ?Collection
    {
        return $this->locationSensor;
    }

    public function setLocationSensor(?Collection $locationSensor): static
    {
        $this->locationSensor = $locationSensor;

        return $this;
    }

    public function addLocationSensor(LocationSensors $locationSensor): static
    {
        $this->locationSensor->add($locationSensor);

        return $this;
    }

    public function getLocationMagnitude(): ?Collection
    {
        return $this->locationMagnitude;
    }

    public function setLocationMagnitude(?Collection $locationMagnitude): static
    {
        $this->locationMagnitude = $locationMagnitude;

        return $this;
    }


    public function addLocationMagnitude(LocationMagnitudes $locationMagnitude): static
    {
        $this->locationMagnitude->add($locationMagnitude);

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'description' => $this->description,
            'lat_dd' => $this->lat_dd,
            'lon_dd' => $this->lon_dd,
            'altitude_m' => $this->altitude_m,
            'address' => $this->address,
            'active' => $this->active,
            'created_at' => $this->created_at
        ];        
    }
}
