<?php

namespace App\Model;

class District
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $area;

    /**
     * @var string
     */
    private $population;

    /**
     * @param string $name
     * @param string $area
     * @param string $population
     */
    public function __construct(string $name, string $area, string $population)
    {
        $this->name       = $name;
        $this->area       = $area;
        $this->population = $population;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getArea(): string
    {
        return $this->area;
    }

    /**
     * @return string
     */
    public function getPopulation(): string
    {
        return $this->population;
    }
}
