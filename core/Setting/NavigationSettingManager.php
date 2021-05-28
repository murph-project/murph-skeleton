<?php

namespace App\Core\Setting;

use App\Core\Entity\NavigationSetting;
use App\Core\Entity\Site\Navigation;
use App\Core\Factory\NavigationSettingFactory;
use App\Core\Manager\EntityManager;
use App\Core\Repository\NavigationSettingRepositoryQuery;
use App\Core\Repository\Site\NavigationRepositoryQuery;

/**
 * class NavigationSettingManager.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class NavigationSettingManager
{
    protected EntityManager $entityManager;
    protected NavigationSettingRepositoryQuery $query;
    protected NavigationRepositoryQuery $navigationQuery;
    protected NavigationSettingFactory $factory;

    public function __construct(
        EntityManager $entityManager,
        NavigationSettingRepositoryQuery $query,
        NavigationRepositoryQuery $navigationQuery,
        NavigationSettingFactory $factory
    ) {
        $this->entityManager = $entityManager;
        $this->query = $query;
        $this->navigationQuery = $navigationQuery;
        $this->factory = $factory;
    }

    public function init($navigation, string $code, string $section, string $label, $value = null)
    {
        $entity = $this->get($this->getNavigation($navigation), $code);
        $isNew = null === $entity;

        if ($isNew) {
            $entity = $this->factory->create($navigation, $code);
            $entity->setValue($value);
        }

        $entity
            ->setSection($section)
            ->setLabel($label)
        ;

        if ($isNew) {
            $this->entityManager->create($entity);
        } else {
            $this->entityManager->update($entity);
        }
    }

    public function get($navigation, string $code): ?NavigationSetting
    {
        return $this->query->create()
            ->andWhere('.navigation = :navigation')
            ->andWhere('.code = :code')
            ->setParameter(':navigation', $this->getNavigation($navigation)->getId())
            ->setParameter(':code', $code)
            ->findOne()
        ;
    }

    public function set($navigation, string $code, $value): bool
    {
        $entity = $this->get($this->getNavigation($navigation), $code);

        if (!$entity) {
            return false;
        }

        $entity->setValue($value);
        $this->entityManager->update($entity);

        return true;
    }

    protected function getNavigation($navigation): Navigation
    {
        if ($navigation instanceof Navigation) {
            return $navigation;
        }

        $entity = $this->navigationQuery->create()
            ->where('.code', $navigation)
            ->findOne()
        ;

        if (!$entity) {
            throw new \RuntimeException(sprintf('The navigation "%s" does not exist.', $navigation));
        }

        return $entity;
    }
}
