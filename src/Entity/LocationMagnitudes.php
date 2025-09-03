<?php

namespace App\Entity;

use App\Repository\LocationMagnitudesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationMagnitudesRepository::class)]
class LocationMagnitudes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(nullable: true)]
    private ?float $min_acceptable = null;

    #[ORM\Column(nullable: true)]
    private ?float $max_acceptable = null;

    #[ORM\Column(nullable: true)]
    private ?float $alert_low = null;

    #[ORM\Column(nullable: true)]
    private ?float $alert_high = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sampling_plan = null;

    #[ORM\Column]
    private ?bool $required = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?locations $location_id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?magnitudes $magnitude_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocationId(): ?int
    {
        return $this->location_id;
    }



    public function getMinAcceptable(): ?float
    {
        return $this->min_acceptable;
    }

    public function setMinAcceptable(?float $min_acceptable): static
    {
        $this->min_acceptable = $min_acceptable;

        return $this;
    }

    public function getMaxAcceptable(): ?float
    {
        return $this->max_acceptable;
    }

    public function setMaxAcceptable(?float $max_acceptable): static
    {
        $this->max_acceptable = $max_acceptable;

        return $this;
    }

    public function getAlertLow(): ?float
    {
        return $this->alert_low;
    }

    public function setAlertLow(?float $alert_low): static
    {
        $this->alert_low = $alert_low;

        return $this;
    }

    public function getAlertHigh(): ?float
    {
        return $this->alert_high;
    }

    public function setAlertHigh(?float $alert_high): static
    {
        $this->alert_high = $alert_high;

        return $this;
    }

    public function getSamplingPlan(): ?string
    {
        return $this->sampling_plan;
    }

    public function setSamplingPlan(?string $sampling_plan): static
    {
        $this->sampling_plan = $sampling_plan;

        return $this;
    }

    public function isRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): static
    {
        $this->required = $required;

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

    public function setLocationId(?locations $location_id): static
    {
        $this->location_id = $location_id;

        return $this;
    }

    public function getMagnitudeId(): ?magnitudes
    {
        return $this->magnitude_id;
    }

    public function setMagnitudeId(magnitudes $magnitude_id): static
    {
        $this->magnitude_id = $magnitude_id;

        return $this;
    }
}
