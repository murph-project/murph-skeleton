<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use App\Core\Factory\FactoryInterface;
use <?= $entity ?> as Entity;

class <?= $class_name; ?> implements FactoryInterface
{
    public function create(): Entity
    {
        return new Entity();
    }
}
