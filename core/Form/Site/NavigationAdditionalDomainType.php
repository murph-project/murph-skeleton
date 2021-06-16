<?php

namespace App\Core\Form\Site;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class NavigationAdditionalDomainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'domain',
            TextType::class,
            [
                'label' => 'Domain',
                'required' => true,
                'help' => 'Regular expression: do not add the delimiter',
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'type',
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
