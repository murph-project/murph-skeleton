<?php

namespace App\Core\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * class UrlGenerator.
 *
 * @author Simon Vieille <simon@deblan.fr>
 * @Annotation
 */
class UrlGenerator
{
    public string $service;

    public string $method;

    public array $options = [];
}
