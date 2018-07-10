<?php

namespace App\Service\District\Provider;

use App\Entity\City;
use App\Model\District;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

class GdanskProvider implements ProviderInterface
{
    /**
     * @const string
     */
    const SUPPORTED_CITY_NAME = 'Gdańsk';

    /**
     * @const string
     */
    const IMPORT_URL = 'http://www.gdansk.pl/matarnia';

    /**
     * @const string
     */
    const IMPORT_INFO_URL = 'http://www.gdansk.pl/';

    /**
     * @const string
     */
    const EMPTY = 'Wszystkie';

    /**
     * @const int
     */
    const INDEX_AREA = 1;

    /**
     * @const int
     */
    const INDEX_POPULATION = 2;

    /**
     * @return string
     */
    public function getName()
    {
        return self::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(City $city)
    {
        if (self::SUPPORTED_CITY_NAME !== $city->getName()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getDistricts()
    {
        $client  = new Client();
        $crawler = $client->request(Request::METHOD_GET, self::IMPORT_URL);

        $districts = $crawler
            ->filter('.lista-dzielnic ul li')
            ->each(
                function (Crawler $node, $i) {
                    $a    = $node->filter('a');
                    $span = $node->filter('span');

                    if (0 < $a->count()) {
                        $name = $a->text();
                        $id   = $a->attr('href');
                    } else {
                        $name = $span->text();
                        $id   = $span->attr('href');
                    }

                    if (self::EMPTY === $name) {
                        return;
                    }

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
     * @param string $id
     *
     * @return array
     */
    private function getInfo($id)
    {
        $importUrl = sprintf(
            '%s%s',
            self::IMPORT_INFO_URL,
            $id
        );

        $info    = [];
        $client  = new Client();
        $crawler = $client->request(Request::METHOD_GET, $importUrl);

        $crawler
            ->filter('.opis div')
            ->each(
                function (Crawler $node, $i) use (&$info) {
                    preg_match(
                        '/([0-9]+\,?[0-9].)/',
                        $node->text(),
                        $value
                    );

                    if (self::INDEX_AREA === $i) {
                        $info['area'] = (float) str_replace(',', '.', $value[0]);
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
