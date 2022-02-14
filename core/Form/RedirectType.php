<?php

namespace App\Core\Form;

use App\Core\Entity\Redirect;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class RedirectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            'scheme',
            ChoiceType::class,
            [
                'label' => 'Scheme',
                'required' => true,
                'choices' => [
                    'http(s)://' => 'all',
                    'http://' => 'http',
                    'https://' => 'https',
                ],
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
                'help' => 'Regular expression: do not add the delimiter',
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'domainType',
            ChoiceType::class,
            [
                'label' => 'Type',
                'required' => true,
                'choices' => [
                    'Domain' => 'domain',
                    'Regular expression' => 'regexp',
                ],
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'rule',
            TextType::class,
            [
                'label' => 'Rule',
                'required' => true,
                'attr' => [
                ],
                'help' => 'Regular expression: do not add the delimiter',
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'ruleType',
            ChoiceType::class,
            [
                'label' => 'Type',
                'required' => true,
                'choices' => [
                    'Path' => 'path',
                    'Regular expression' => 'regexp',
                ],
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'location',
            TextType::class,
            [
                'label' => 'Location',
                'required' => true,
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'redirectCode',
            ChoiceType::class,
            [
                'label' => 'Code',
                'required' => true,
                'choices' => [
                    '301 - Moved Permanently' => 301,
                    '307 - Temporary Redirect' => 307,
                ],
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'isEnabled',
            CheckboxType::class,
            [
                'label' => 'Enabled',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'reuseQueryString',
            CheckboxType::class,
            [
                'label' => 'Reuse the query string',
                'required' => false,
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
            'data_class' => Redirect::class,
        ]);
    }
}
