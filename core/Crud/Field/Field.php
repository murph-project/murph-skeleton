<?php

namespace App\Core\Crud\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Twig\Environment;

/**
 * class Field.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
abstract class Field
{
    public function buildView(Environment $twig, $entity, array $options, ?string $locale = null)
    {
        return $twig->render($this->getView($options), [
            'entity' => $entity,
            'value' => $this->getValue($entity, $options, $locale),
            'options' => $options,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setDefaults([
            'property' => null,
            'property_builder' => null,
            'view' => null,
            'raw' => false,
            'sort' => null,
            'href' => null,
            'href_attr' => [],
            'attr' => [],
        ]);

        $resolver->setRequired('view');
        $resolver->setAllowedTypes('property', ['null', 'string']);
        $resolver->setAllowedTypes('view', 'string');
        $resolver->setAllowedTypes('attr', 'array');
        $resolver->setAllowedTypes('href', ['null', 'string', 'callable']);
        $resolver->setAllowedTypes('href_attr', 'array', 'callable');
        $resolver->setAllowedTypes('raw', 'boolean');
        $resolver->setAllowedTypes('property_builder', ['null', 'callable']);
        $resolver->setAllowedValues('sort', function($value) {
            if ($value === null) {
                return true;
            }

            if (!is_array($value)) {
                return false;
            }

            $isValidParam1 = !empty($value[0]) && is_string($value[0]);
            $isValidParam2 = !empty($value[1]) && (is_string($value[1]) || is_callable($value[1]));

            return $isValidParam1 && $isValidParam2;
        });

        return $resolver;
    }

    protected function getValue($entity, array $options, ?string $locale = null)
    {
        if (null !== $options['property']) {
            $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()->getPropertyAccessor();

            try {
                $value = $propertyAccessor->getValue($entity, $options['property']);
            } catch (NoSuchPropertyException $e) {
                if (null !== $locale) {
                    $value = $propertyAccessor->getValue($entity->translate($locale), $options['property']);
                } else {
                    throw $e;
                }
            }
        } elseif (null !== $options['property_builder']) {
            $value = call_user_func($options['property_builder'], $entity, $options);
        } else {
            $value = null;
        }

        return $value;
    }

    protected function getView(array $options)
    {
        return $options['view'];
    }
}
