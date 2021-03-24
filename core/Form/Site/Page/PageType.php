<?php

namespace App\Core\Form\Site\Page;

use App\Core\Entity\Site\Page\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'metaTitle',
            TextType::class,
            [
                'label' => 'Titre',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'metaDescrition',
            TextType::class,
            [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'ogTitle',
            TextType::class,
            [
                'label' => 'Titre',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'ogDescription',
            TextType::class,
            [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                ],
            ]
        );

        $builder->add(
            'ogImage',
            FileType::class,
            [
                'label' => 'Image',
                'required' => false,
                'attr' => [
                ],
                'constraints' => [
                    new Image(),
                ],
            ]
        );

        $builder->add(
            'template',
            ChoiceType::class,
            [
                'label' => 'Rendu',
                'required' => true,
                'choices' => call_user_func(function () use ($options) {
                    $choices = [];

                    foreach ($options['pageConfiguration']->getTemplates() as $template) {
                        $choices[$template['name']] = $template['file'];
                    }

                    return $choices;
                }),
                'attr' => [
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->getData()->buildForm($builder);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
            'pageConfiguration' => null,
        ]);
    }
}
