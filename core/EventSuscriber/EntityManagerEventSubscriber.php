<?php

namespace App\Core\EventSuscriber;

use App\Core\Event\EntityManager\EntityManagerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * class EntityManagerEventSubscriber.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
abstract class EntityManagerEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            EntityManagerEvent::CREATE_EVENT => 'onCreate',
            EntityManagerEvent::UPDATE_EVENT => 'onUpdate',
            EntityManagerEvent::DELETE_EVENT => 'onDelete',
            EntityManagerEvent::PRE_CREATE_EVENT => 'onPreCreate',
            EntityManagerEvent::PRE_UPDATE_EVENT => 'onPreUpdate',
            EntityManagerEvent::PRE_DELETE_EVENT => 'onPreDelete',
        ];
    }

    public function onCreate(EntityManagerEvent $event)
    {
    }

    public function onUpdate(EntityManagerEvent $event)
    {
    }

    public function onDelete(EntityManagerEvent $event)
    {
    }

    public function onPreCreate(EntityManagerEvent $event)
    {
    }

    public function onPreUpdate(EntityManagerEvent $event)
    {
    }

    public function onPreDelete(EntityManagerEvent $event)
    {
    }
}
