<?php

namespace App\Core\Crud\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * class TextField.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class TextField extends Field
{
    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'view' => '@Core/admin/crud/field/text.html.twig',
        ]);

        return $resolver;
    }
}
