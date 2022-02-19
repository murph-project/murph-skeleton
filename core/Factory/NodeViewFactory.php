<?php

namespace App\Core\Factory;

use App\Core\Entity\NodeView as Entity;
use App\Core\Entity\Site\Node;

class NodeViewFactory implements FactoryInterface
{
    public function create(Node $node, string $path): Entity
    {
        $entity = new Entity();
        $entity
            ->setNode($node)
            ->setPath($path)
            ->setDate(new \DateTime())
        ;

        return $entity;
    }
}
