<?php

namespace App\Core\Crud\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * class ButtonField.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class ButtonField extends Field
{
    public function buildView(Environment $twig, $entity, array $options, ?string $locale = null)
    {
        if (isset($options['button_attr_builder']) && is_callable($options['button_attr_builder'])) {
            $options['button_attr'] = (array) call_user_func($options['button_attr_builder'], $entity, $options);
        }

        return parent::buildView($twig, $entity, $options, $locale);
    }

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'view' => '@Core/admin/crud/field/button.html.twig',
            'button_attr' => [],
            'button_attr_builder' => null,
            'button_tag' => 'button',
        ]);

        $resolver->setAllowedTypes('button_attr', ['array']);
        $resolver->setAllowedTypes('button_tag', ['string']);
        $resolver->setAllowedTypes('button_attr_builder', ['null', 'callable']);

        return $resolver;
    }
}
