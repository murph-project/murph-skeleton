<?php

namespace App\Core\Form\Site;

use App\Core\Entity\Site\Navigation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NavigationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'label',
            TextType::class,
            [
                'label' => 'Label',
                'required' => true,
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'code',
            TextType::class,
            [
                'label' => 'Code',
                'required' => true,
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'domain',
            TextType::class,
            [
                'label' => 'Domain',
                'required' => true,
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'forceDomain',
            CheckboxType::class,
            [
                'label' => 'Force this domain',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'additionalDomains',
            CollectionType::class,
            [
                'entry_type' => NavigationAdditionalDomainType::class,
                'label' => 'Additional domains',
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ]
        );

        $builder->add(
            'locale',
            TextType::class,
            [
                'label' => 'Locale',
                'required' => true,
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 10]),
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Navigation::class,
        ]);
    }
}
