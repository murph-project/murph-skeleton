<?php

namespace App\Core\EventSuscriber\Site;

use App\Core\Entity\EntityInterface;
use App\Core\Entity\Site\Menu;
use App\Core\Entity\Site\Navigation;
use App\Core\Entity\Site\Node;
use App\Core\Event\EntityManager\EntityManagerEvent;
use App\Core\EventSuscriber\EntityManagerEventSubscriber;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Core\Cache\SymfonyCacheManager;

/**
 * class SiteEventSubscriber.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class SiteEventSubscriber extends EntityManagerEventSubscriber
{
    protected KernelInterface $kernel;
    protected SymfonyCacheManager $cacheManager;

    public function __construct(KernelInterface $kernel, SymfonyCacheManager $cacheManager)
    {
        $this->kernel = $kernel;
        $this->cacheManager = $cacheManager;
    }

    public function support(EntityInterface $entity)
    {
        return $entity instanceof Node || $entity instanceof Menu || $entity instanceof Navigation;
    }

    public function onUpdate(EntityManagerEvent $event)
    {
        if (!$this->support($event->getEntity())) {
            return;
        }

        $this->cacheManager->cleanRouting();
    }

    public function onCreate(EntityManagerEvent $event)
    {
        return $this->onUpdate($event);
    }

    public function onDelete(EntityManagerEvent $event)
    {
        return $this->onUpdate($event);
    }
}
