<?php

namespace App\Core\Event\EntityManager;

use App\Core\Entity\EntityInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * class EntityManagerEvent.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class EntityManagerEvent extends Event
{
    const CREATE_EVENT = 'entity_manager_event.create';
    const UPDATE_EVENT = 'entity_manager_event.update';
    const DELETE_EVENT = 'entity_manager_event.delete';
    const PRE_CREATE_EVENT = 'entity_manager_event.pre_create';
    const PRE_UPDATE_EVENT = 'entity_manager_event.pre_update';
    const PRE_DELETE_EVENT = 'entity_manager_event.pre_delete';

    protected EntityInterface $entity;

    public function __construct(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): EntityInterface
    {
        return $this->entity;
    }
}
