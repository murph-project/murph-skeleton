<?php

namespace App\Core\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CollectionType as BaseCollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends BaseCollectionType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars = array_replace($view->vars, [
            'collection_name' => $options['collection_name'],
            'label_add' => $options['label_add'],
            'label_delete' => $options['label_delete'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'collection_name' => '',
            'label_add' => 'Add',
            'label_delete' => 'Delete',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'murph_collection';
    }
}
