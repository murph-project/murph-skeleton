<?php

namespace App\Core\Setting;

use App\Core\Entity\Setting;
use App\Core\Factory\SettingFactory;
use App\Core\Manager\EntityManager;
use App\Core\Repository\SettingRepositoryQuery;

/**
 * class SettingManager.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class SettingManager
{
    protected EntityManager $entityManager;
    protected SettingRepositoryQuery $query;
    protected SettingFactory $factory;

    public function __construct(EntityManager $entityManager, SettingRepositoryQuery $query, SettingFactory $factory)
    {
        $this->entityManager = $entityManager;
        $this->query = $query;
        $this->factory = $factory;
    }

    public function init(string $code, string $section, string $label, $value = null)
    {
        $entity = $this->get($code);
        $isNew = null === $entity;

        if ($isNew) {
            $entity = $this->factory->create($code);
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

    public function get(string $code): ?Setting
    {
        return $this->query->create()
            ->where('.code = :code')
            ->setParameter(':code', $code)
            ->findOne()
        ;
    }

    public function set(string $code, $value): bool
    {
        $entity = $this->get($code);

        if (!$entity) {
            return false;
        }

        $entity->setValue($value);
        $this->entityManager->update($entity);

        return true;
    }
}
