<?php

namespace App\Core\Form\Site;

use App\Core\Entity\Site\Navigation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class NavigationType extends AbstractType
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
                'label' => 'Nom de domaine',
                'required' => true,
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
            'data_class' => Navigation::class,
        ]);
    }
}
