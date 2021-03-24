<?php

namespace App\Core\Form\Site\Page;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class TextareaBlockType extends TextBlockType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'value',
            TextareaType::class,
            array_merge([
                'required' => false,
                'label' => false,
            ], $options['options']),
        );
    }
}
