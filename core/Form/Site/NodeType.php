<?php

namespace App\Core\Form\Site;

use App\Core\Entity\Site\Node;
use App\Core\Entity\Site\Page\Page;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class NodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'label',
            TextType::class,
            [
                'label' => 'Libellé',
                'required' => true,
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'url',
            TextType::class,
            [
                'label' => 'URL',
                'required' => false,
                'help' => 'Laisser vide pour une génération automatique',
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'code',
            TextType::class,
            [
                'label' => 'Code',
                'required' => false,
                'help' => 'Sans espace, en minusule, sans caractère spécial',
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'controller',
            TextType::class,
            [
                'label' => 'Contrôleur',
                'required' => false,
                'help' => 'Laisser vide pour utiliser celui par défaut. Notation : App\\Controller\\FooController::barAction',
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $actions = [
            'Nouvelle page' => 'new',
            'Associer à une page existante' => 'existing',
            'Aucune page' => 'none',
        ];

        if ($builder->getData()->getId()) {
            $actions['Garder la configuration actuelle'] = 'keep';
        }

        $builder->add(
            'pageAction',
            ChoiceType::class,
            [
                'label' => false,
                'required' => true,
                'expanded' => true,
                'mapped' => false,
                'choices' => $actions,
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'pageType',
            ChoiceType::class,
            [
                'label' => false,
                'required' => true,
                'mapped' => false,
                'choices' => call_user_func(function () use ($options) {
                    $choices = [];

                    foreach ($options['pages'] as $page) {
                        $choices[$page->getName()] = $page->getClassName();
                    }

                    return $choices;
                }),
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'pageEntity',
            EntityType::class,
            [
                'label' => false,
                'required' => true,
                'mapped' => false,
                'class' => Page::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC')
                    ;
                },
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'parameters',
            CollectionType::class,
            [
                'entry_type' => NodeParameterType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ]
        );

        $builder->add(
            'attributes',
            CollectionType::class,
            [
                'entry_type' => NodeAttributeType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ]
        );

        $builder->add(
            'sitemapParameters',
            NodeSitemapParametersType::class,
            [
                'label' => false,
            ]
        );

        if (null === $builder->getData()->getId()) {
            $builder->add(
                'position',
                ChoiceType::class,
                [
                    'label' => 'Position',
                    'required' => true,
                    'mapped' => false,
                    'choices' => [
                        'Après' => 'after',
                        'Avant' => 'before',
                        'En dessous' => 'above',
                    ],
                    'attr' => [
                    ],
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Node::class,
            'pages' => [],
        ]);
    }
}
