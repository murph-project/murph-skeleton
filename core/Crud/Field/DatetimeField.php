<?php

namespace App\Core\Crud\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * class DatetimeField.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class DatetimeField extends Field
{
    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'view' => '@Core/admin/crud/field/date.html.twig',
            'format' => 'Y-m-d H:i:s',
        ]);

        return $resolver;
    }
}
