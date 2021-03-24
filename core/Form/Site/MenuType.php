<?php

namespace App\Core\Form\Site;

use App\Core\Entity\Site\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MenuType extends AbstractType
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
