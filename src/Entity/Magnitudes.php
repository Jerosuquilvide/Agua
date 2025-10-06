<?php

namespace App\Entity;

use App\Repository\MagnitudesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MagnitudesRepository::class)]
class Magnitudes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $group_name = null;

    #[ORM\Column(length: 255)]
    private ?string $name_en = null;

    #[ORM\Column(length: 255)]
    private ?string $abbreviation = null;  

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $wqx_code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $wmo_code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $iso_ieee_code = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $decimals = null;

    #[ORM\Column]
    private ?bool $allow_negative = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $min_value = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $max_value = null;

    #[ORM\OneToMany(targetEntity: SensorMagnitudes::class, mappedBy: 'magnitude', cascade: ['persist', 'remove'])]
    private ?Collection $sensorMagnitudes = null;
    
    #[ORM\ManyToOne(targetEntity: Units::class, inversedBy: 'magnitudes')]
    #[ORM\JoinColumn(nullable: false)]
    private Units $unit;

    #[ORM\OneToMany(targetEntity: LocationMagnitudes::class, mappedBy: 'magnitude', cascade: ['persist', 'remove'])]
    private ?Collection $locationMagnitudes = null; 

    #[ORM\OneToMany(targetEntity: MeasuredValues::class, mappedBy: 'magnitude', cascade: ['persist', 'remove'])]
    private ?Collection $measuredValues = null; 
    

    public function __construct()
    {
        $this->sensorMagnitudes = new ArrayCollection();
        $this->locationMagnitudes = new ArrayCollection();
        $this->measuredValues = new ArrayCollection();
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

    public function getGroupName(): ?string
    {
        return $this->group_name;
    }

    public function setGroupName(string $group_name): static
    {
        $this->group_name = $group_name;

        return $this;
    }

    public function getNameEn(): ?string
    {
        return $this->name_en;
    }

    public function setNameEn(string $name_en): static
    {
        $this->name_en = $name_en;

        return $this;
    }

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(string $abbreviation): static
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    public function getWqxCode(): ?string
    {
        return $this->wqx_code;
    }

    public function setWqxCode(?string $wqx_code): static
    {
        $this->wqx_code = $wqx_code;

        return $this;
    }

    public function getWmoCode(): ?string
    {
        return $this->wmo_code;
    }

    public function setWmoCode(?string $wmo_code): static
    {
        $this->wmo_code = $wmo_code;

        return $this;
    }

    public function getIsoIeeeCode(): ?string
    {
        return $this->iso_ieee_code;
    }

    public function setIsoIeeeCode(?string $iso_ieee_code): static
    {
        $this->iso_ieee_code = $iso_ieee_code;

        return $this;
    }

    public function getDecimals(): ?int
    {
        return $this->decimals;
    }

    public function setDecimals(?int $decimals): static
    {
        $this->decimals = $decimals;

        return $this;
    }

    public function isAllowNegative(): ?bool
    {
        return $this->allow_negative;
    }

    public function setAllowNegative(bool $allow_negative): static
    {
        $this->allow_negative = $allow_negative;

        return $this;
    }

    public function getMinValue(): ?string
    {
        return $this->min_value;
    }

    public function setMinValue(?string $min_value): static
    {
        $this->min_value = $min_value;

        return $this;
    }

    public function getMaxValue(): ?string
    {
        return $this->max_value;
    }

    public function setMaxValue(?string $max_value): static
    {
        $this->max_value = $max_value;

        return $this;
    }

    public function getSensorMagnitudes(): Collection
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

    public function getUnit(): Collection
    {
        return $this->unit;
    }

    public function setUnit(?Units $unit): static
    {
        $this->unit = $unit;

        return $this;
    }


    public function getLocationMagnitudes(): Collection
    {
        return $this->locationMagnitudes;
    }

    public function setLocationMagnitudes(?Collection $locationMagnitudes): static
    {
        $this->locationMagnitudes = $locationMagnitudes;

        return $this;
    }

    public function addLocationMagnitude(LocationMagnitudes $locationMagnitude): static
    {
        $this->locationMagnitudes->add($locationMagnitude);

        return $this;
    }

    public function getMeasuredValues(): Collection
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

    public function jsonSerialize(): array
    {
        return [
            'id'    => $this->id,
            'group_name'  => $this->group_name,
            'name_en' => $this->name_en,
            'abbreviation' => $this->abbreviation,
            'wqx_code' => $this->wqx_code,
            'wmo_code' => $this->wmo_code,
            'iso_ieee_code' => $this->iso_ieee_code,
            'decimals' => $this->decimals,
            'allow_negative' => $this->allow_negative,
            'min_value' => $this->min_value,
            'max_value' => $this->max_value            
        ];        
    }
}
