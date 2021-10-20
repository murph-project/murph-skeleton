<?php

namespace App\Core\Event\Setting;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * class NavigationSettingEvent.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class NavigationSettingEvent extends Event
{
    const INIT_EVENT = 'navigation_setting_event.init';
    const FORM_INIT_EVENT = 'navigation_setting_event.form_init';

    protected $data;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setOption(string $key, $value): self
    {
        $this->data['options'][$key] = $value;

        return $this;
    }
}
