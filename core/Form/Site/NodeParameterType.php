<?php

namespace App\Core\Form\Site;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class NodeParameterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'Name',
                'required' => true,
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'defaultValue',
            TextType::class,
            [
                'label' => 'Default value',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'requirement',
            TextType::class,
            [
                'label' => 'Requirement',
                'help' => 'Regular expression',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
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
