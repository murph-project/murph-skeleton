<?php

namespace App\Core\Crud\Field;

use App\Core\Crud\Exception\CrudConfigurationException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Twig\Environment;

/**
 * class Field.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
abstract class Field
{
    public function buildView(Environment $twig, $entity, array $options)
    {
        return $twig->render($this->getView($options), [
            'value' => $this->getValue($entity, $options),
            'options' => $options,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setDefaults([
            'property' => null,
            'property_builder' => null,
            'view' => null,
            'attr' => [],
        ]);

        $resolver->setRequired('view');
        $resolver->setAllowedTypes('property', ['null', 'string']);
        $resolver->setAllowedTypes('view', 'string');
        $resolver->setAllowedTypes('attr', 'array');
        $resolver->setAllowedTypes('property_builder', ['null', 'callable']);

        return $resolver;
    }

    protected function getValue($entity, array $options)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->getPropertyAccessor()
        ;

        if (null !== $options['property']) {
            $value = $propertyAccessor->getValue($entity, $options['property']);
        } elseif (null !== $options['property_builder']) {
            $value = call_user_func($options['property_builder'], $entity, $options);
        } else {
            throw new CrudConfigurationException('Unable to get the value. One of "property" and "property_builder" is required.');
        }

        return $value;
    }

    protected function getView(array $options)
    {
        return $options['view'];
    }
}
