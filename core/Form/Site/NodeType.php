<?php

namespace App\Core\Form\Site;

use App\Core\Entity\Site\Node;
use App\Core\Entity\Site\Page\Page;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            'url',
            TextType::class,
            [
                'label' => 'URL',
                'required' => false,
                'help' => 'Leave blank for automatic generation',
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'disableUrl',
            CheckboxType::class,
            [
                'label' => 'Disable URL',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'enableAnalytics',
            CheckboxType::class,
            [
                'label' => 'Enable analytics',
                'required' => false,
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
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'contentType',
            TextType::class,
            [
                'label' => 'Content type',
                'required' => false,
                'help' => 'Leave blank equals "text/html"',
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'controller',
            ChoiceType::class,
            [
                'label' => 'Controller',
                'required' => false,
                'help' => 'Leave blank to use the default one',
                'choices' => call_user_func(function () use ($options) {
                    $choices = [];

                    foreach ($options['controllers'] as $controller) {
                        $choices[$controller->getName()] = $controller->getAction();
                    }

                    return $choices;
                }),
            ]
        );

        if (count($options['roles']) > 0) {
            $builder->add(
                'securityRoles',
                ChoiceType::class,
                [
                    'label' => 'Roles',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => call_user_func(function () use ($options) {
                        $choices = [];

                        foreach ($options['roles'] as $role) {
                            $choices[$role->getName()] = $role->getRole();
                        }

                        return $choices;
                    }),
                ]
            );

            $builder->add(
                'securityOperator',
                ChoiceType::class,
                [
                    'label' => 'Condition',
                    'required' => true,
                    'choices' => [
                        'At least one role' => 'or',
                        'All roles' => 'and',
                    ],
                ]
            );
        }

        $actions = [
            'New page' => 'new',
            'Use an existing page' => 'existing',
            'Alias element' => 'alias',
            'No page' => 'none',
        ];

        if ($builder->getData()->getId()) {
            $actions['Keep the current configuration'] = 'keep';
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
            'aliasNode',
            EntityType::class,
            [
                'label' => false,
                'required' => false,
                'class' => Node::class,
                'choice_label' => 'label',
                'choices' => call_user_func(function () use ($options, $builder) {
                    $nodes = [];

                    foreach ($options['navigation']->getMenus() as $menu) {
                        $nodes = array_merge(
                            $nodes,
                            $menu->getRootNode()->getAllChildren()->toArray()
                        );
                    }

                    foreach ($nodes as $k => $value) {
                        if ($value->getId() === $builder->getData()->getId()) {
                            unset($nodes[$k]);
                        }
                    }

                    return $nodes;
                }),
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
                        'After' => 'after',
                        'Before' => 'before',
                        'Above' => 'above',
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
            'controllers' => [],
            'roles' => [],
            'navigation' => null,
        ]);
    }
}
