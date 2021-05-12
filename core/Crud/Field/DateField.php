<?php

namespace App\Core\Crud\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * class DateField.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class DateField extends Field
{
    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'view' => '@Core/admin/crud/field/date.html.twig',
            'format' => 'Y-m-d',
        ]);

        return $resolver;
    }
}
