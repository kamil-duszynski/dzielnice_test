<?php

namespace App\Service\District;

use App\Entity\City;
use App\Entity\District;
use App\Service\District\Provider\ProviderInterface;
use Doctrine\ORM\EntityManagerInterface;

class DistrictService
{
    /**
     * @var ProviderInterface[]
     */
    private $providers;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \App\Model\District[]
     */
    private $districts;

    /**
     * @var District[]
     */
    private $availableDistricts;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ProviderInterface $provider
     */
    public function addProvider(ProviderInterface $provider)
    {
        $this->providers[$provider->getName()] = $provider;
    }

    /**
     * @param City $city
     */
    public function import(City $city)
    {
        foreach ($this->providers as $provider) {
            if (false === $provider->supports($city)) {
                continue;
            }

            $this->districts = $provider->getDistricts();
        }

        if (true === empty($this->districts)) {
            return;
        }

        // pobieramy istniejace w bazie ddzielnice, żeby je aktualizować a nie duplikować
        $names = array_map(
            function (\App\Model\District $district) {
                return $district->getName();
            },
            $this->districts
        );

        $this->availableDistricts = $this->entityManager
            ->getRepository('App:District')
            ->getAllByNames($names)
        ;

        foreach ($this->districts as $district) {
            $name       = $district->getName();
            $population = $district->getPopulation();
            $area       = $district->getArea();
            $districtDB = $this->getExisted($district);

            if (null === $districtDB) {
                $districtDB = new District();
            }

            $districtDB
                ->setName($name)
                ->setArea($area)
                ->setPopulation($population)
                ->setCity($city)
            ;

            $this->entityManager->persist($districtDB);
        }

        $this->entityManager->flush();
    }

    /**
     * @param \App\Model\District $district
     *
     * @return District|null
     */
    public function getExisted(\App\Model\District $district)
    {
        foreach ($this->availableDistricts as $availableDistrict) {
            if ($availableDistrict->getName() === $district->getName()) {
                return $availableDistrict;
            }
        }

        return null;
    }
}
