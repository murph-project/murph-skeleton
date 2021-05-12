<?php

namespace App\Core\Crud\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * class ButtonField.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class ButtonField extends Field
{
    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'view' => '@Core/admin/crud/field/button.html.twig',
            'button_attr' => [],
            'button_tag' => 'button',
        ]);

        $resolver->setAllowedTypes('button_attr', ['array']);
        $resolver->setAllowedTypes('button_tag', ['string']);

        return $resolver;
    }
}
