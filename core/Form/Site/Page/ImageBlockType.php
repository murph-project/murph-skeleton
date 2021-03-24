<?php

namespace App\Core\Form\Site\Page;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;

class ImageBlockType extends FileBlockType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'value',
            FileType::class,
            array_merge([
                'required' => false,
                'label' => false,
                'constraints' => [
                    new Image(),
                ],
            ], $options['options']),
        );
    }
}
