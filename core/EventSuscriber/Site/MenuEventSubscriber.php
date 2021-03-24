<?php

namespace App\Core\EventSuscriber\Site;

use App\Core\Entity\EntityInterface;
use App\Core\Entity\Site\Menu;
use App\Core\Event\EntityManager\EntityManagerEvent;
use App\Core\EventSuscriber\EntityManagerEventSubscriber;
use App\Core\Factory\Site\NodeFactory;
use App\Core\Manager\EntityManager;
use App\Core\Repository\Site\NodeRepository;
use App\Core\Slugify\CodeSlugify;

/**
 * class MenuEventSubscriber.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class MenuEventSubscriber extends EntityManagerEventSubscriber
{
    protected NodeFactory $nodeFactory;
    protected NodeRepository $nodeRepository;
    protected EntityManager $entityManager;
    protected CodeSlugify $slugify;

    public function __construct(
        NodeFactory $nodeFactory,
        NodeRepository $nodeRepository,
        EntityManager $entityManager,
        CodeSlugify $slugify
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->nodeRepository = $nodeRepository;
        $this->entityManager = $entityManager;
        $this->slugify = $slugify;
    }

    public function support(EntityInterface $entity)
    {
        return $entity instanceof Menu;
    }

    public function onPreUpdate(EntityManagerEvent $event)
    {
        if (!$this->support($event->getEntity())) {
            return;
        }

        $menu = $event->getEntity();
        $menu->setCode($this->slugify->slugify($menu->getCode()));
    }

    public function onCreate(EntityManagerEvent $event)
    {
        if (!$this->support($event->getEntity())) {
            return;
        }

        $menu = $event->getEntity();

        if (0 !== count($menu->getNodes())) {
            return;
        }

        $rootNode = $this->nodeFactory->create($menu);
        $childNode = $this->nodeFactory->create($menu, '/');
        $childNode
            ->setParent($rootNode)
            ->setLabel('Premier élément')
        ;

        $menu->setRootNode($rootNode);

        $this->entityManager->getEntityManager()->persist($rootNode);
        $this->entityManager->getEntityManager()->persist($childNode);

        $this->entityManager->getEntityManager()->persist($menu);
        $this->entityManager->flush();

        $this->nodeRepository->persistAsFirstChild($childNode, $rootNode);
    }

    public function onUpdate(EntityManagerEvent $event)
    {
        return $this->onCreate($event);
    }

    public function onPreCreate(EntityManagerEvent $event)
    {
        return $this->onPreUpdate($event);
    }
}
