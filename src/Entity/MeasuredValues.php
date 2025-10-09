<?php

namespace App\Entity;

use App\Repository\MeasuredValuesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeasuredValuesRepository::class)]
class MeasuredValues
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Measurements::class, inversedBy: 'measuredValues')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Measurements $measurement = null;

    #[ORM\ManyToOne(targetEntity: Magnitudes::class, inversedBy: 'measuredValues')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Magnitudes $magnitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $value_numeric = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $qc_flag = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $taken_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comments = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $snapshot_min_acceptable = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $snapshot_max_acceptable = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $snapshot_alert_low = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $snapshot_alert_high = null;

    #[ORM\Column(nullable: true)]
    private ?bool $snapshot_allow_negative = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getMeasurement(): ?Measurements
    {
        return $this->measurement;
    }

    public function setMeasurement(?Measurements $measurement): static
    {
        $this->measurement = $measurement;

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

    public function getValueNumeric(): ?string
    {
        return $this->value_numeric;
    }

    public function setValueNumeric(string $value_numeric): static
    {
        $this->value_numeric = $value_numeric;

        return $this;
    }

    public function getQcFlag(): ?string
    {
        return $this->qc_flag;
    }

    public function setQcFlag(?string $qc_flag): static
    {
        $this->qc_flag = $qc_flag;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTakenAt(): ?\DateTimeImmutable
    {
        return $this->taken_at;
    }

    public function setTakenAt(?\DateTimeImmutable $taken_at): static
    {
        $this->taken_at = $taken_at;

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

    public function getSnapshotMinAcceptable(): ?string
    {
        return $this->snapshot_min_acceptable;
    }

    public function setSnapshotMinAcceptable(?string $snapshot_min_acceptable): static
    {
        $this->snapshot_min_acceptable = $snapshot_min_acceptable;

        return $this;
    }

    public function getSnapshotMaxAcceptable(): ?string
    {
        return $this->snapshot_max_acceptable;
    }

    public function setSnapshotMaxAcceptable(?string $snapshot_max_acceptable): static
    {
        $this->snapshot_max_acceptable = $snapshot_max_acceptable;

        return $this;
    }

    public function getSnapshotAlertLow(): ?string
    {
        return $this->snapshot_alert_low;
    }

    public function setSnapshotAlertLow(?string $snapshot_alert_low): static
    {
        $this->snapshot_alert_low = $snapshot_alert_low;

        return $this;
    }

    public function getSnapshotAlertHigh(): ?string
    {
        return $this->snapshot_alert_high;
    }

    public function setSnapshotAlertHigh(?string $snapshot_alert_high): static
    {
        $this->snapshot_alert_high = $snapshot_alert_high;

        return $this;
    }

    public function isSnapshotAllowNegative(): ?bool
    {
        return $this->snapshot_allow_negative;
    }

    public function setSnapshotAllowNegative(?bool $snapshot_allow_negative): static
    {
        $this->snapshot_allow_negative = $snapshot_allow_negative;

        return $this;
    }

    public function jsonSerialize(): array{
        return [
            'id' => $this->id,
            'measurement' => isset($this->measurement) ? $this->measurement->getId() : null,
            'magnitude' => isset($this->magnitude) ? $this->magnitude->getId() : null,
            'value_numeric' => $this->value_numeric,
            'qc_flag' => $this->qc_flag,
            'status' => $this->status,
            'taken_at' => $this->taken_at,
            'comments' => $this->comments,
            'snapshot_min_acceptable' => $this->snapshot_min_acceptable,
            'snapshot_max_acceptable' => $this->snapshot_max_acceptable,
            'snapshot_alert_low' => $this->snapshot_alert_low,
            'snapshot_alert_high' => $this->snapshot_alert_high,
            'snapshot_allow_negative' => $this->snapshot_allow_negative,
        ];
    }
}
