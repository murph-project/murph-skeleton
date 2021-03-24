<?php

namespace App\Core\Factory\Site\Page;

use App\Core\Entity\Site\Page\Page;

/**
 * class PageFactory.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class PageFactory
{
    public function create(string $className, string $name): Page
    {
        $entity = new $className();
        $entity->setName($name);

        return $entity;
    }
}
