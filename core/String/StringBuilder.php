<?php

namespace App\Core\String;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use function Symfony\Component\String\u;

/**
 * class StringBuilder.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class StringBuilder
{
    protected PropertyAccessor $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->getPropertyAccessor()
        ;
    }

    /**
     * Builds a string and inject values from given object.
     *
     * @param mixed $object
     */
    public function build(string $format, $object): string
    {
        if (!is_array($object) && !is_object($object)) {
            return $format;
        }

        preg_match_all('/\{([a-zA-Z0-9\.]+)\}/i', $format, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $propertyValue = $this->propertyAccessor->getValue($object, $match[1]);

            $format = u($format)->replace($match[0], $propertyValue);
        }

        return $format;
    }
}
