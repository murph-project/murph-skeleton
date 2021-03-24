<?php

namespace App\Core\Factory\Site;

use App\Core\Entity\Site\Navigation;

/**
 * class NavigationFactory.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class NavigationFactory
{
    public function create(): Navigation
    {
        return new Navigation();
    }
}
