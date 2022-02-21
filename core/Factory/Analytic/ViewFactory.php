<?php

namespace App\Core\Factory\Analytic;

use App\Core\Entity\Analytic\View as Entity;
use App\Core\Entity\Site\Node;
use App\Core\Factory\FactoryInterface;

class ViewFactory implements FactoryInterface
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
