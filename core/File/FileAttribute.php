<?php

namespace App\Core\File;

use Symfony\Component\HttpFoundation\File\File;

/**
 * class FileAttribute.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class FileAttribute
{
    public static function handleFile($attribute, string $class = null)
    {
        if (null === $class) {
            $class = File::class;
        }

        if (is_string($attribute)) {
            if (file_exists($attribute)) {
                return new $class($attribute);
            }

            return null;
        }

        return $attribute;
    }
}
