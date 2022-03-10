<?php

namespace App\Core\EventSubscriber;

use App\Core\Event\EntityManager\EntityManagerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * class EntityManagerEventSubscriber.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
abstract class EntityManagerEventSubscriber implements EventSubscriberInterface
{
    protected static int $priority = 0;

    public static function getSubscribedEvents()
    {
        return [
            EntityManagerEvent::CREATE_EVENT => ['onCreate', self::$priority],
            EntityManagerEvent::UPDATE_EVENT => ['onUpdate', self::$priority],
            EntityManagerEvent::DELETE_EVENT => ['onDelete', self::$priority],
            EntityManagerEvent::PRE_CREATE_EVENT => ['onPreCreate', self::$priority],
            EntityManagerEvent::PRE_UPDATE_EVENT => ['onPreUpdate', self::$priority],
            EntityManagerEvent::PRE_DELETE_EVENT => ['onPreDelete', self::$priority],
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
