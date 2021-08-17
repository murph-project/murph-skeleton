<?php

namespace App\Core\Form\Site\Page;

use App\Core\Entity\Site\Page\ChoiceBlock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoiceBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'value',
            ChoiceType::class,
            array_merge([
                'required' => false,
                'label' => false,
            ], $options['options']),
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChoiceBlock::class,
            'options' => [],
        ]);
    }
}
