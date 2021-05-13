<?php

namespace App\Core\Crud\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * class ImageField.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class ImageField extends Field
{
    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'view' => '@Core/admin/crud/field/image.html.twig',
            'image_attr' => [],
        ]);

        $resolver->setAllowedTypes('image_attr', ['array']);

        return $resolver;
    }
}
