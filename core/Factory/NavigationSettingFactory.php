<?php

namespace App\Core\Factory;

use App\Core\Entity\NavigationSetting;
use App\Core\Entity\Site\Navigation;

/**
 * class NavigationSettingFactory.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class NavigationSettingFactory implements FactoryInterface
{
    public function create(Navigation $navigation, string $code): NavigationSetting
    {
        $entity = new NavigationSetting();

        $entity
            ->setNavigation($navigation)
            ->setCode($code)
        ;

        return $entity;
    }
}
