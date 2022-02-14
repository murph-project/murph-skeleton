<?php

namespace App\Core\Form\Filter;

use App\Core\Entity\Redirect;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RedirectFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'label',
            TextType::class,
            [
                'label' => 'Label',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'scheme',
            ChoiceType::class,
            [
                'label' => 'Scheme',
                'required' => false,
                'choices' => [
                    'http(s)://' => 'all',
                    'http://' => 'http',
                    'https://' => 'https',
                ],
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'domain',
            TextType::class,
            [
                'label' => 'Domain',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'domainType',
            ChoiceType::class,
            [
                'label' => 'Type',
                'required' => false,
                'choices' => [
                    'Domain' => 'domain',
                    'Regular expression' => 'regexp',
                ],
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'rule',
            TextType::class,
            [
                'label' => '',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'ruleType',
            ChoiceType::class,
            [
                'label' => 'Type',
                'required' => false,
                'choices' => [
                    'Path' => 'path',
                    'Regular expression' => 'regexp',
                ],
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'location',
            TextType::class,
            [
                'label' => 'Location',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'redirectCode',
            ChoiceType::class,
            [
                'label' => 'Code',
                'required' => false,
                'choices' => [
                    '301 - Moved Permanently' => 301,
                    '307 - Temporary Redirect' => 307,
                ],
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => false,
        ]);
    }
}
