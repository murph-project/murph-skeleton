<?php

namespace App\Core\Form\Site;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class NodeSitemapParametersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'isVisible',
            CheckboxType::class,
            [
                'label' => 'Visible',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'priority',
            ChoiceType::class,
            [
                'label' => 'Priority',
                'required' => true,
                'attr' => [
                ],
                'choices' => call_user_func(function () {
                    $choices = [];

                    for ($u = 0; $u <= 10; ++$u) {
                        $choices[$u] = $u / 10;
                    }

                    return $choices;
                }),
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'changeFrequency',
            ChoiceType::class,
            [
                'label' => 'Frequency of change',
                'required' => true,
                'attr' => [
                ],
                'choices' => [
                    'Toujours' => 'always',
                    'Toutes les heures' => 'hourly',
                    'Quotidienne' => 'daily',
                    'Hebdomadaire' => 'weekly',
                    'Mensuelle' => 'monthly',
                    'Annuelle' => 'yearly',
                    'Jamais' => 'never',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
