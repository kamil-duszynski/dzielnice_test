<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DistrictRepository")
 */
class District
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="districts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $area;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $population;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return District
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return City
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param City $city
     *
     * @return District
     */
    public function setCity(City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return float
     */
    public function getArea(): ?float
    {
        return $this->area;
    }

    /**
     * @param float $area
     *
     * @return District
     */
    public function setArea(float $area): self
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return int
     */
    public function getPopulation(): ?int
    {
        return $this->population;
    }

    /**
     * @param int $population
     *
     * @return District
     */
    public function setPopulation(int $population): self
    {
        $this->population = $population;

        return $this;
    }
}
