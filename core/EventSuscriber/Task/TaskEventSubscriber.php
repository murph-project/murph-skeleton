<?php

namespace App\Core\EventSuscriber\Task;

use App\Core\Event\Setting\SettingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Core\Event\Task\TaskRunRequestedEvent;
use App\Core\Event\Task\TaskInitEvent;

/**
 * class TaskEventSubscriber.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
abstract class TaskEventSubscriber implements EventSubscriberInterface
{
    protected static int $priority = 0;

    public static function getSubscribedEvents()
    {
        return [
            TaskInitEvent::INIT_EVENT => ['onInit', self::$priority],
            TaskRunRequestedEvent::RUN_REQUEST_EVENT => ['onRunRequest', self::$priority],
        ];
    }

    public function onInit(TaskInitEvent $event)
    {
    }

    public function onRunRequest(TaskRunRequestedEvent $event)
    {
    }
}
