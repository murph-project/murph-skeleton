<?php

namespace App\Core\Factory\Analytic;

use App\Core\Entity\Analytic\Referer as Entity;
use App\Core\Entity\Site\Node;
use App\Core\Factory\FactoryInterface;

class RefererFactory implements FactoryInterface
{
    public function create(Node $node, string $uri): Entity
    {
        $entity = new Entity();
        $entity
            ->setNode($node)
            ->setUri($uri)
            ->setDate(new \DateTime())
        ;

        return $entity;
    }
}
