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

    #[ORM\ManyToOne(targetEntity: Sensors::class, inversedBy: 'sensorMagnitudes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Sensors $sensor = null;

    #[ORM\ManyToOne(targetEntity: Magnitudes::class, inversedBy: 'sensorMagnitudes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Magnitudes $magnitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $value_min = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $value_max = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $resolution = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $accuracy = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $calibrated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $channel_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $notes = null;

    public function __construct()
    {
        $this->measurements = new ArrayCollection();
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

    public function getSensor(): ?Sensors
    {
        return $this->sensor;
    }

    public function setSensor(?Sensors $sensor): static
    {
        $this->sensor = $sensor;
        return $this;
    }

    public function getMagnitude(): ?Magnitudes
    {
        return $this->magnitude;
    }

    public function setMagnitude(?Magnitudes $magnitude): static
    {
        $this->magnitude = $magnitude;
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

    public function getaccuracy(): ?string
    {
        return $this->accuracy;
    }

    public function setaccuracy(?string $accuracy): static
    {
        $this->accuracy = $accuracy;
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
    
    public function jsonSerialize(): array{
        return [
            'id' => $this->id,
            'sensor' => isset($this->sensor) ? $this->sensor->getId() : null,
            'magnitude' => isset($this->magnitude) ? $this->magnitude->getId() : null,
            'value_min' => $this->value_min,
            'value_max' => $this->value_max,
            'resolution' => $this->resolution,
            'accuracy' => $this->accuracy,
            'calibrated_at' => $this->calibrated_at,
            'channel_name' => $this->channel_name,
            'notes' => $this->notes,
        ];
    }

}
