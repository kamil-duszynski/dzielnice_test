<?php

namespace App\Service\District\Provider;

use App\Entity\City;
use App\Model\District;

interface ProviderInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param City $city
     *
     * @return bool
     */
    public function supports(City $city);

    /**
     * @return District[]
     */
    public function getDistricts();
}
