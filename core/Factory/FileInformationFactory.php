<?php

namespace App\Core\Factory;

use App\Core\Entity\FileInformation as Entity;

class FileInformationFactory implements FactoryInterface
{
    public function create(string $id): Entity
    {
        $entity = new Entity();
        $entity->setId($id);

        return $entity;
    }
}
