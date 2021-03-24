<?php

namespace App\Core\Factory\Site;

use App\Core\Entity\Site\Menu;
use App\Core\Entity\Site\Navigation;

/**
 * class MenuFactory.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class MenuFactory
{
    public function create(?Navigation $navigation = null): Menu
    {
        $entity = new Menu();

        if (null !== $navigation) {
            $entity->setNavigation($navigation);
        }

        return $entity;
    }
}
