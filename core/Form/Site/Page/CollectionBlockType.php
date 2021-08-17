<?php

namespace App\Core\Form\Site\Page;

use App\Core\Entity\Site\Page\Block;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionBlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'value',
            $options['collection_type'],
            array_merge([
                'required' => false,
                'label' => false,
            ], $options['options']),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'collection_name' => $options['collection_name'],
            'label_add' => $options['label_add'],
            'label_delete' => $options['label_delete'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Block::class,
            'collection_type' => CollectionType::class,
            'collection_name' => '',
            'label_add' => 'Add',
            'label_delete' => 'Delete',
            'options' => [],
        ]);
    }
}
