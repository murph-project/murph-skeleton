<?php

namespace App\Core\EventSuscriber\Task;

use App\Core\Cache\SymfonyCacheManager;
use App\Core\Event\Task\TaskInitEvent;
use App\Core\Event\Task\TaskRunRequestedEvent;

/**
 * class CacheCleanTaskEventSubscriber.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class CacheCleanTaskEventSubscriber extends TaskEventSubscriber
{
    protected SymfonyCacheManager $cacheManager;

    public function __construct(SymfonyCacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    public function onInit(TaskInitEvent $event)
    {
        $event->addTask('cache:clear', 'Clean all cache', '♻️ Cache');
    }

    public function onRunRequest(TaskRunRequestedEvent $event)
    {
        if ('cache:clear' !== $event->getTask()) {
            return;
        }

        $this->cacheManager->cleanAll($event->getOutput());
    }
}
