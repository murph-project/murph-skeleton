<?php

namespace App\Core\Factory;

use App\Core\Entity\Setting;

/**
 * class SettingFactory.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class SettingFactory implements FactoryInterface
{
    public function create(string $code): Setting
    {
        $entity = new Setting();

        $entity->setCode($code);

        return $entity;
    }
}
