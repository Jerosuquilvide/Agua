<?php

namespace App\Entity;

use App\Repository\MeasurementsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeasurementsRepository::class)]
class Measurements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Sensors::class, inversedBy: 'measurements', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Sensors $sensor = null;

    #[ORM\ManyToOne(targetEntity: Locations::class, inversedBy: 'measurements', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Locations $location = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'entered_measurements', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Users $entered_by = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'sampled_measurements', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Users $sampled_by = null;

    #[ORM\OneToMany(targetEntity: MeasuredValues::class, mappedBy: 'measurement', cascade: ['persist', 'remove'])]
    private ?Collection $measuredValues = null; 
    
    #[ORM\Column]
    private ?\DateTimeImmutable $registered_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $sampled_at = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    private ?string $source = null;

    #[ORM\Column(nullable: true)]
    private ?string $batch_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comments = null;

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

    public function getLocation(): ?Locations
    {
        return $this->location;
    }

    public function setLocation(?Locations $location): static
    {
        $this->location = $location;
        return $this;
    }

    public function getEnteredBy(): ?Users
    {
        return $this->entered_by;
    }

    public function setEnteredBy(?Users $entered_by): static
    {
        $this->entered_by = $entered_by;
        return $this;
    }

    public function getSampledBy(): ?Users
    {
        return $this->sampled_by;
    }

    public function setSampledBy(?Users $sampled_by): static
    {
        $this->sampled_by = $sampled_by;
        return $this;
    }

    public function getMeasuredValues(): ?Collection
    {
        return $this->measuredValues;
    }

    public function setMeasuredValues(?Collection $measuredValues): static
    {
        $this->measuredValues = $measuredValues;
        return $this;
    }

    public function addMeasuredValue(MeasuredValues $measuredValue): static
    {
        $this->measuredValues->add($measuredValue);

        return $this;
    }

    public function getRegisteredAt(): ?\DateTimeImmutable
    {
        return $this->registered_at;
    }

    public function setRegisteredAt(\DateTimeImmutable $registered_at): static
    {
        $this->registered_at = $registered_at;
        return $this;
    }

    public function getSampledAt(): ?\DateTimeImmutable
    {
        return $this->sampled_at;
    }

    public function setSampledAt(\DateTimeImmutable $sampled_at): static
    {
        $this->sampled_at = $sampled_at;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;
        return $this;
    }

    public function getBatchId(): ?string
    {
        return $this->batch_id;
    }

    public function setBatchId(?string $batch_id): static
    {
        $this->batch_id = $batch_id;
        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): static
    {
        $this->comments = $comments;
        return $this;
    }

    public function jsonSerialize(): array{
        return [
            'id' => $this->id,
            'sensor' => isset($this->sensor) ? $this->sensor->getId() : null,
            'location' => isset($this->location) ? $this->location->getId() : null,
            'entered_by' => isset($this->entered_by) ? $this->entered_by->getId() : null,
            'sampled_by' => isset($this->sampled_by) ? $this->sampled_by->getId() : null,
            'registered_at' => $this->registered_at,
            'sampled_at' => $this->sampled_at,
            'status' => $this->status,
            'source' => $this->source,
            'batch_id' => $this->batch_id,
            'comments' => $this->comments,
        ];
    }

}
