<?php

namespace App\Core\Factory\Site;

use App\Core\Entity\Site\Menu;
use App\Core\Entity\Site\Node;
use App\Core\Factory\FactoryInterface;

/**
 * class NodeFactory.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class NodeFactory implements FactoryInterface
{
    public function create(?Menu $menu = null, string $url = null): Node
    {
        $entity = new Node();

        if (null !== $menu) {
            $entity->setMenu($menu);
        }

        if (null !== $url) {
            $entity->setUrl($url);
        }

        return $entity;
    }
}
