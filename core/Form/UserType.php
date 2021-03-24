<?php

namespace App\Core\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'email',
            EmailType::class,
            [
                'label' => 'E-mail',
                'required' => true,
                'attr' => [
                ],
                'constraints' => [
                    new Email(),
                ],
            ]
        );

        $builder->add(
            'displayName',
            TextType::class,
            [
                'label' => 'Display name',
                'required' => true,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'isAdmin',
            CheckboxType::class,
            [
                'label' => 'Administrator',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'isWriter',
            CheckboxType::class,
            [
                'label' => 'Writer',
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
            'data_class' => User::class,
        ]);
    }
}
