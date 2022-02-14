<?php

namespace App\Core\Factory;

use App\Core\Factory\FactoryInterface;
use App\Core\Entity\Redirect as Entity;

class RedirectFactory implements FactoryInterface
{
    public function create(): Entity
    {
        return new Entity();
    }
}
