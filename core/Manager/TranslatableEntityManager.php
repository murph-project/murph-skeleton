<?php

namespace App\Core\Manager;

use App\Core\Entity\EntityInterface;

/**
 * class TranslatableEntityManager.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class TranslatableEntityManager extends EntityManager
{
    protected function persist(EntityInterface $entity)
    {
        $this->entityManager->persist($entity);
        $entity->mergeNewTranslations();
        $this->flush();
    }
}
