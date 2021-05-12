<?php

namespace App\Core\Factory\Site;

use App\Core\Entity\Site\Navigation;
use App\Core\Factory\FactoryInterface;

/**
 * class NavigationFactory.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class NavigationFactory implements FactoryInterface
{
    public function create(): Navigation
    {
        return new Navigation();
    }
}
