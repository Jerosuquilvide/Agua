<?php

namespace App\Entity;

use App\Repository\UnitsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UnitsRepository::class)]
class Units
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $acum_code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $uncefact_code = null;

    #[ORM\Column(length: 255)]
    private ?string $display = null;

    #[ORM\ManyToOne(inversedBy: 'unit_id')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Magnitudes $magnitudes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getAcumCode(): ?string
    {
        return $this->acum_code;
    }

    public function setAcumCode(string $acum_code): static
    {
        $this->acum_code = $acum_code;

        return $this;
    }

    public function getUncefactCode(): ?string
    {
        return $this->uncefact_code;
    }

    public function setUncefactCode(?string $uncefact_code): static
    {
        $this->uncefact_code = $uncefact_code;

        return $this;
    }

    public function getDisplay(): ?string
    {
        return $this->display;
    }

    public function setDisplay(string $display): static
    {
        $this->display = $display;

        return $this;
    }

    public function getMagnitudes(): ?Magnitudes
    {
        return $this->magnitudes;
    }

    public function setMagnitudes(?Magnitudes $magnitudes): static
    {
        $this->magnitudes = $magnitudes;

        return $this;
    }
}
