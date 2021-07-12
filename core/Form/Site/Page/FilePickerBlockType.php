<?php

namespace App\Core\Form\Site\Page;

use App\Core\Form\FileManager\FilePickerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use App\Core\Entity\Site\Page\Block;

class FilePickerBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'value',
            FilePickerType::class,
            array_merge([
                'required' => false,
                'label' => false,
            ], $options['options']),
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Block::class,
            'block_prefix' => 'file_picker_page_block',
            'options' => [],
        ]);
    }
}
