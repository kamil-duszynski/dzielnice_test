<?php

namespace App\Form;

use App\Entity\City;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DistrictForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label'    => 'Nazwa',
                    'required' => true,
                ]
            )
            ->add(
                'city',
                EntityType::class,
                [
                    'label'         => 'Miasto',
                    'required'      => true,
                    'class'         => City::class,
                    'choice_label'  => 'name',
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
                    'required' => true,
                ]
            )
            ->add(
                'area',
                TextType::class,
                [
                    'label'    => 'Powierzchnia',
                    'required' => true,
                ]
            )
        ;
    }
}
