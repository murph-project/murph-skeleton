<?php

namespace App\Core\Form\FileManager;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class FileUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'files',
            FileType::class,
            [
                'label' => 'Files',
                'required' => true,
                'multiple' => true,
                'attr' => [
                    'webkitdirectory' => '',
                    'directory' => '',
                    'mozdirectory' => '',
                ],
                'constraints' => [
                    new All([
                        new File([
                            'mimeTypes' => $options['mimes'],
                        ]),
                    ]),
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mimes' => [],
        ]);
    }
}
