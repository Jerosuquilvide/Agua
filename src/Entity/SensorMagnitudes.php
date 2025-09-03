<?php

namespace App\Entity;

use App\Repository\SensorMagnitudesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SensorMagnitudesRepository::class)]
class SensorMagnitudes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Sensors>
     */
    #[ORM\OneToMany(targetEntity: Sensors::class, mappedBy: 'sensorMagnitudes')]
    private Collection $sensor_id;


    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Units $unit_id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $value_min = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $value_max = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $resolution = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $acurracy = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $calibrated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $channel_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $notes = null;

    /**
     * @var Collection<int, Magnitudes>
     */
    #[ORM\OneToMany(targetEntity: Magnitudes::class, mappedBy: 'sensorMagnitudes')]
    private Collection $magnitude_id;

    public function __construct()
    {
        $this->sensor_id = new ArrayCollection();
        $this->magnitude_id = new ArrayCollection();
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

    /**
     * @return Collection<int, Sensors>
     */
    public function getSensorId(): Collection
    {
        return $this->sensor_id;
    }

    public function addSensorId(Sensors $sensorId): static
    {
        if (!$this->sensor_id->contains($sensorId)) {
            $this->sensor_id->add($sensorId);
            $sensorId->setSensorMagnitudes($this);
        }

        return $this;
    }

    public function removeSensorId(Sensors $sensorId): static
    {
        if ($this->sensor_id->removeElement($sensorId)) {
            // set the owning side to null (unless already changed)
            if ($sensorId->getSensorMagnitudes() === $this) {
                $sensorId->setSensorMagnitudes(null);
            }
        }

        return $this;
    }

    public function getMagnitudeId(): ?int
    {
        return $this->magnitude_id;
    }

    public function setMagnitudeId(int $magnitude_id): static
    {
        $this->magnitude_id = $magnitude_id;

        return $this;
    }

    public function getUnitId(): ?Units
    {
        return $this->unit_id;
    }

    public function setUnitId(?Units $unit_id): static
    {
        $this->unit_id = $unit_id;

        return $this;
    }

    public function getValueMin(): ?string
    {
        return $this->value_min;
    }

    public function setValueMin(?string $value_min): static
    {
        $this->value_min = $value_min;

        return $this;
    }

    public function getValueMax(): ?string
    {
        return $this->value_max;
    }

    public function setValueMax(?string $value_max): static
    {
        $this->value_max = $value_max;

        return $this;
    }

    public function getResolution(): ?string
    {
        return $this->resolution;
    }

    public function setResolution(?string $resolution): static
    {
        $this->resolution = $resolution;

        return $this;
    }

    public function getAcurracy(): ?string
    {
        return $this->acurracy;
    }

    public function setAcurracy(?string $acurracy): static
    {
        $this->acurracy = $acurracy;

        return $this;
    }

    public function getCalibratedAt(): ?\DateTimeImmutable
    {
        return $this->calibrated_at;
    }

    public function setCalibratedAt(?\DateTimeImmutable $calibrated_at): static
    {
        $this->calibrated_at = $calibrated_at;

        return $this;
    }

    public function getChannelName(): ?string
    {
        return $this->channel_name;
    }

    public function setChannelName(?string $channel_name): static
    {
        $this->channel_name = $channel_name;

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

    public function addMagnitudeId(Magnitudes $magnitudeId): static
    {
        if (!$this->magnitude_id->contains($magnitudeId)) {
            $this->magnitude_id->add($magnitudeId);
            $magnitudeId->setSensorMagnitudes($this);
        }

        return $this;
    }

    public function removeMagnitudeId(Magnitudes $magnitudeId): static
    {
        if ($this->magnitude_id->removeElement($magnitudeId)) {
            // set the owning side to null (unless already changed)
            if ($magnitudeId->getSensorMagnitudes() === $this) {
                $magnitudeId->setSensorMagnitudes(null);
            }
        }

        return $this;
    }
}
