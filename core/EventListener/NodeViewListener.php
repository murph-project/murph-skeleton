<?php

namespace App\Core\EventListener;

use App\Core\Factory\NodeViewFactory;
use App\Core\Manager\EntityManager;
use App\Core\Repository\NodeViewRepositoryQuery;
use App\Core\Repository\Site\NodeRepositoryQuery;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use App\Core\Repository\Site\NodeRepository;

/**
 * class NodeViewListener.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class NodeViewListener
{
    protected NodeRepository $nodeRepository;
    protected NodeViewRepositoryQuery $nodeViewRepositoryQuery;
    protected NodeViewFactory $nodeViewFactory;
    protected EntityManager $manager;

    public function __construct(
        NodeRepository $nodeRepository,
        NodeViewRepositoryQuery $nodeViewRepositoryQuery,
        NodeViewFactory $nodeViewFactory,
        EntityManager $manager
    ) {
        $this->nodeRepository = $nodeRepository;
        $this->nodeViewRepositoryQuery = $nodeViewRepositoryQuery;
        $this->nodeViewFactory = $nodeViewFactory;
        $this->manager = $manager;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->has('_node')) {
            return;
        }

        $node = $this->nodeRepository->findOneById($request->attributes->get('_node'));

        if (!$node || !$node->getEnableViewCounter()) {
            return;
        }

        $nodeView = $this->nodeViewRepositoryQuery->create()
            ->filterByRequest($request)
            ->andWhere('.date=CURRENT_DATE()')
            ->findOne()
        ;

        if (!$nodeView) {
            $nodeView = $this->nodeViewFactory->create($node, $request->getPathInfo());
        }

        $nodeView->addView();

        if ($nodeView->getId()) {
            $this->manager->update($nodeView);
        } else {
            $this->manager->create($nodeView);
        }
    }
}
