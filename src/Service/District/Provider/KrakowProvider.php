<?php

namespace App\Service\District\Provider;

use App\Entity\City;
use App\Helper\StringConversionHelper;
use App\Model\District;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

class KrakowProvider implements ProviderInterface
{
    /**
     * @const string
     */
    const SUPPORTED_CITY_NAME = 'Kraków';

    /**
     * @const string
     */
    const IMPORT_URL = 'http://www.bip.krakow.pl/?bip_id=1&mmi=10501';

    /**
     * @const string
     */
    const IMPORT_INFO_URL = 'http://appimeri.um.krakow.pl/app-pub-dzl/pages/DzlViewGlw.jsf';

    /**
     * @const int
     */
    const INDEX_AREA = 0;

    /**
     * @const int
     */
    const INDEX_POPULATION = 1;

    /**
     * @return string
     */
    public function getName()
    {
        return self::class;
    }

    /**
     * @param City $city
     *
     * @return bool
     */
    public function supports(City $city)
    {
        if (self::SUPPORTED_CITY_NAME !== $city->getName()) {
            return false;
        }

        return true;
    }

    /**
     * @return District[]
     */
    public function getDistricts()
    {
        $client  = new Client();
        $crawler = $client->request(Request::METHOD_GET, self::IMPORT_URL);

        // pobieram url z iframe
        $url = $crawler
            ->filter('iframe[name="FRAME4"]')
            ->attr('src')
        ;

        // zaciagam dane z contentu iframe
        $crawler = $client->request(Request::METHOD_GET, $url);

        $districts = $crawler
            ->filter('form select option')
            ->each(
                function (Crawler $node, $i) {
                    // czyścimy nazwę
                    $name = str_replace(
                        ['Dzielnica ', 'I ', 'I', 'V ', 'V', 'X ', 'X'],
                        '',
                        $node->text()
                    );

                    $id   = $node->attr('value');
                    $info = $this->getInfo($id);

                    return new District(
                        $name,
                        $info['area'],
                        $info['population']
                    );
                }
            )
        ;

        return array_filter($districts);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    private function getInfo(int $id)
    {
        $importUrl = sprintf(
            '%s?id=%d',
            self::IMPORT_INFO_URL,
            $id
        );

        $info    = [];
        $client  = new Client();
        $crawler = $client->request(Request::METHOD_GET, $importUrl);

        $crawler
            ->filter('#mainDiv table tr table tr')
            ->each(
                function (Crawler $node, $i) use (&$info) {
                    preg_match(
                        '/([0-9]+\,?[0-9].)/',
                        $node->text(),
                        $value
                    );

                    if (self::INDEX_AREA === $i) {
                        $areaInHa = (float) str_replace(',', '.', $value[0]);
                        $area     = round($areaInHa * 0.01, 2);

                        $info['area'] = $area;
                    }

                    if (self::INDEX_POPULATION === $i) {
                        $info['population'] = (int) $value[0];
                    }
                }
            )
        ;

        return $info;
    }
}
