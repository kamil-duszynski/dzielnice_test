<?php

namespace App\Form;

use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DistrictFilterForm extends AbstractType
{
    /**
     * @var CityRepository
     */
    private $repository;

    /**
     * @param CityRepository $repository
     */
    public function __construct(CityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'id',
                TextType::class,
                [
                    'label'    => 'ID',
                    'required' => false,
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'label'    => 'Nazwa',
                    'required' => false,
                ]
            )
            ->add(
                'city',
                EntityType::class,
                [
                    'label'         => 'Miasto',
                    'class'         => City::class,
                    'choice_label'  => 'name',
                    'required'      => false,
                    'placeholder'   => '- wybierz miasto -',
                    'query_builder' => function (EntityRepository $entityRepository) {
                        return $entityRepository->createQueryBuilder('c')
                            ->orderBy('c.name', Criteria::ASC);
                    },
                ]
            )
            ->add(
                'population',
                TextType::class,
                [
                    'label'    => 'Populacja',
                    'required' => false,
                ]
            )
            ->add(
                'area',
                TextType::class,
                [
                    'label'    => 'Powierzchnia',
                    'required' => false,
                ]
            )
        ;

        $repository = $this->repository;

        $builder->get('city')
            ->addModelTransformer(
                new CallbackTransformer(
                    function ($id) use ($repository) {
                        return $repository->find($id);
                    },
                    function (City $city) {
                        return $city->getId();
                    }
                )
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return null;
    }
}
