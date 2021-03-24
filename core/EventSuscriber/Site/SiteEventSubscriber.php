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

/**
 * class SiteEventSubscriber.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class SiteEventSubscriber extends EntityManagerEventSubscriber
{
    protected KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
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

        $this->cleanCache();
    }

    public function onCreate(EntityManagerEvent $event)
    {
        return $this->onUpdate($event);
    }

    public function onDelete(EntityManagerEvent $event)
    {
        return $this->onUpdate($event);
    }

    protected function cleanCache()
    {
        $finder = new Finder();
        $finder
            ->in($this->kernel->getCacheDir())
            ->name('url_*.php*')
        ;

        foreach ($finder as $file) {
            unlink((string) $file->getPathname());
        }
    }
}
