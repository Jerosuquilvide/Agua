<?php

namespace App\Entity;

use App\Repository\MeasurementsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeasurementsRepository::class)]
class Measurements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'measurements', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sensors $sensor = null;

    #[ORM\OneToOne(inversedBy: 'measurements', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Locations $location = null;

    #[ORM\OneToOne(inversedBy: 'measurements', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $entered_by = null;

    #[ORM\OneToOne(inversedBy: 'measurements', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sampled = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $registered_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $sampled_at = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    private ?string $source = null;

    #[ORM\Column(nullable: true)]
    private ?int $batch_id = null;

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

    public function getSensorId(): ?Sensors
    {
        return $this->sensor;
    }

    public function setSensorId(Sensors $sensor_id): static
    {
        $this->sensor = $sensor_id;

        return $this;
    }

    public function getLocationId(): ?Locations
    {
        return $this->location;
    }

    public function setLocationId(Locations $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getEnteredBy(): ?User
    {
        return $this->entered_by;
    }

    public function setEnteredBy(User $entered): static
    {
        $this->entered_by = $entered;

        return $this;
    }

    public function getSampledBy(): ?User
    {
        return $this->sampled;
    }

    public function setSampledBy(User $sampled): static
    {
        $this->sampled = $sampled;

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

    public function getBatchId(): ?int
    {
        return $this->batch_id;
    }

    public function setBatchId(?int $batch_id): static
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
}
