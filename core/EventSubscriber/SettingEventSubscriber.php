<?php

namespace App\Core\EventSubscriber;

use App\Core\Event\Setting\SettingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * class SettingEventSubscriber.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
abstract class SettingEventSubscriber implements EventSubscriberInterface
{
    protected static int $priority = 0;

    public static function getSubscribedEvents()
    {
        return [
            SettingEvent::INIT_EVENT => ['onInit', self::$priority],
            SettingEvent::FORM_INIT_EVENT => ['onFormInit', self::$priority],
        ];
    }

    public function onInit(SettingEvent $event)
    {
    }

    public function onFormInit(SettingEvent $event)
    {
    }
}
