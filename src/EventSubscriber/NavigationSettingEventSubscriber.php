<?php

namespace App\EventSubscriber;

use App\Core\Event\Setting\NavigationSettingEvent;
use App\Core\EventSubscriber\NavigationSettingEventSubscriber as EventSubscriber;
use App\Core\Setting\NavigationSettingManager;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

/**
 * class NavigationSettingEventSubscriber.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class NavigationSettingEventSubscriber extends EventSubscriber
{
    protected NavigationSettingManager $manager;

    public function __construct(NavigationSettingManager $manager)
    {
        $this->manager = $manager;
    }

    public function onInit(NavigationSettingEvent $event)
    {
        $data = $event->getData();
        $navigation = $data['navigation'];

        // $this->manager->init($navigation, 'nav_param1', 'Section', 'Param 1', 'Default value');
        // $this->manager->init($navigation, 'nav_param2', 'Section', 'Param 2', true);
    }

    public function onFormInit(NavigationSettingEvent $event)
    {
        $data = $event->getData();
        $builder = $data['builder'];
        $entity = $data['entity'];

        // if ('nav_param1' === $entity->getCode()) {
        //     $builder->add(
        //         'value',
        //         CheckboxType::class,
        //         [
        //             'label' => $entity->getLabel(),
        //             'required' => false,
        //         ]
        //     );
        // }
        //
        // if ('nav_param2' === $entity->getCode()) {
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
