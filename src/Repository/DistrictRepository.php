<?php

namespace App\Repository;

use App\Entity\District;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method District|null find($id, $lockMode = null, $lockVersion = null)
 * @method District|null findOneBy(array $criteria, array $orderBy = null)
 * @method District[]    findAll()
 * @method District[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DistrictRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, District::class);
    }

    /**
     * @param array $filterParams
     *
     * @return Query
     */
    public function getPaginationQuery(array  $filterParams): Query
    {
        $availableFilters = [
            'id',
            'name',
            'population',
            'area',
            'city',
        ];
        $builder          = $this->createQueryBuilder('d')
            ->select('d')
            ->leftJoin('d.city', 'c')
        ;

        if (true === empty($filterParams)) {
            return $builder->getQuery();
        }

        foreach ($filterParams as $name => $value) {
            if (false === in_array($name, $availableFilters)) {
                continue;
            }

            if (true === empty($value)) {
                continue;
            }

            $parsedValue = sprintf('%s%s%s', '%', $value, '%');
            $closure     = sprintf(
                '%s.%s LIKE :%s',
                'city' === $name ? 'c' : 'd',
                'city' === $name ? 'id' : $name,
                $name
            );

            $builder
                ->andWhere($closure)
                ->setParameter($name, $parsedValue)
            ;
        }

        return $builder->getQuery();
    }

    /**
     * @param array $names
     *
     * @return District[]
     */
    public function getAllByNames(array $names)
    {
        return $this->createQueryBuilder('d')
            ->select('d')
            ->leftJoin('d.city', 'c')
            ->where('d.name IN(:names)')
            ->setParameter('names', $names)
            ->getQuery()
            ->getResult();
    }
}
