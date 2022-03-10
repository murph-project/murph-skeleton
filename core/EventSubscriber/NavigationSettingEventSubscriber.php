<?php

namespace App\Core\EventSubscriber;

use App\Core\Event\Setting\NavigationSettingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * class NavigationSettingEventSubscriber.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
abstract class NavigationSettingEventSubscriber implements EventSubscriberInterface
{
    protected static int $priority = 0;

    public static function getSubscribedEvents()
    {
        return [
            NavigationSettingEvent::INIT_EVENT => ['onInit', self::$priority],
            NavigationSettingEvent::FORM_INIT_EVENT => ['onFormInit', self::$priority],
        ];
    }

    public function onInit(NavigationSettingEvent $event)
    {
    }

    public function onFormInit(NavigationSettingEvent $event)
    {
    }
}
