<?php

namespace App\EventSubscriber;

use App\Core\Event\Setting\SettingEvent;
use App\Core\EventSubscriber\SettingEventSubscriber as EventSubscriber;
use App\Core\Setting\SettingManager;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * class SettingEventSubscriber.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class SettingEventSubscriber extends EventSubscriber
{
    protected SettingManager $manager;

    public function __construct(SettingManager $manager)
    {
        $this->manager = $manager;
    }

    public function onInit(SettingEvent $event)
    {
        // $this->manager->init('myapp_foo', 'My app', 'Foo', 'Default value');
        // $this->manager->init('myapp_bar', 'My app', 'Bar', true);
    }

    public function onFormInit(SettingEvent $event)
    {
        $data = $event->getData();
        $builder = $data['builder'];
        $entity = $data['entity'];

        // if ('myapp_foo' === $entity->getCode()) {
        //     $builder->add(
        //         'value',
        //         TextType::class,
        //         [
        //             'label' => $entity->getLabel(),
        //         ]
        //     );
        // }
        //
        // if ('myapp_bar' === $entity->getCode()) {
        //     $builder->add(
        //         'value',
        //         CheckboxType::class,
        //         [
        //             'label' => $entity->getLabel(),
        //             'required' => false,
        //         ]
        //     );
        // }
    }
}
