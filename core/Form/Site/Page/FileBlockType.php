<?php

namespace App\Core\Form\Site\Page;

use App\Core\Entity\Site\Page\FileBlock;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileBlockType extends TextBlockType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'value',
            FileType::class,
            array_merge([
                'required' => false,
                'label' => false,
            ], $options['options']),
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FileBlock::class,
            'block_prefix' => 'file_block',
            'options' => [],
        ]);
    }
}
