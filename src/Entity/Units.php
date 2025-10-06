<?php

namespace App\Entity;

use App\Repository\UnitsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UnitsRepository::class)]
class Units
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ucum_code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $uncefact_code = null;

    #[ORM\Column(length: 255)]
    private ?string $display = null;

    #[ORM\OneToMany(targetEntity: Magnitudes::class, mappedBy: 'unit')]
    private Collection $magnitudes;

    public function __construct()
    {
        $this->magnitudes = new ArrayCollection();
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

    public function getUcumCode(): ?string
    {
        return $this->ucum_code;
    }

    public function setUcumCode(string $ucum_code): static
    {
        $this->ucum_code = $ucum_code;
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

    /**
     * @return Collection<int, Magnitudes>
     */
    public function getMagnitudes(): Collection
    {
        return $this->magnitudes;
    }

    public function addMagnitude(Magnitudes $magnitude): static
    {
        if (!$this->magnitudes->contains($magnitude)) {
            $this->magnitudes->add($magnitude);
            $magnitude->setUnit($this);
        }
        return $this;
    }

    public function jsonSerialize(): array{
        return [
            'id' => $this->id,
            'ucum_code' => $this->ucum_code,
            'uncefact_code' => $this->uncefact_code,
            'display' => $this->display,
        ];
    }

}
