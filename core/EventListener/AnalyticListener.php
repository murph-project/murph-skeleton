<?php

namespace App\Core\EventListener;

use App\Core\Entity\EntityInterface;
use App\Core\Entity\Site\Node;
use App\Core\Factory\Analytic\RefererFactory;
use App\Core\Factory\Analytic\ViewFactory;
use App\Core\Manager\EntityManager;
use App\Core\Repository\Analytic\RefererRepositoryQuery;
use App\Core\Repository\Analytic\ViewRepositoryQuery;
use App\Core\Repository\Site\NodeRepository;
use DeviceDetector\Cache\PSR6Bridge;
use DeviceDetector\DeviceDetector;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * class AnalyticListener.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class AnalyticListener
{
    protected NodeRepository $nodeRepository;
    protected ViewRepositoryQuery $viewRepositoryQuery;
    protected ViewFactory $viewFactory;
    protected RefererRepositoryQuery $refererRepositoryQuery;
    protected RefererFactory $refererFactory;
    protected EntityManager $manager;
    protected DeviceDetector $deviceDetector;
    protected Request $request;
    protected Node $node;

    public function __construct(
        NodeRepository $nodeRepository,
        ViewRepositoryQuery $viewRepositoryQuery,
        ViewFactory $viewFactory,
        RefererRepositoryQuery $refererRepositoryQuery,
        RefererFactory $refererFactory,
        EntityManager $manager
    ) {
        $this->nodeRepository = $nodeRepository;
        $this->viewRepositoryQuery = $viewRepositoryQuery;
        $this->viewFactory = $viewFactory;
        $this->refererRepositoryQuery = $refererRepositoryQuery;
        $this->refererFactory = $refererFactory;
        $this->manager = $manager;
        $this->createDeviceDetector();
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->has('_node')) {
            return;
        }

        $this->deviceDetector->setUserAgent($request->headers->get('user-agent'));
        $this->deviceDetector->parse();

        if ($this->deviceDetector->isBot()) {
            return;
        }

        $node = $this->nodeRepository->findOneBy([
            'id' => $request->attributes->get('_node'),
            'enableAnalytics' => true,
        ]);

        if (!$node) {
            return;
        }

        $this->node = $node;
        $this->request = $request;

        $this->createView();
        $this->createReferer();
    }

    protected function createDeviceDetector()
    {
        $cache = new ApcuAdapter();

        $this->deviceDetector = new DeviceDetector();
        $this->deviceDetector->setCache(new PSR6Bridge($cache));
    }

    protected function createView()
    {
        $entity = $this->viewRepositoryQuery->create()
            ->filterByRequest($this->request)
            ->andWhere('.date=CURRENT_DATE()')
            ->findOne()
        ;

        if (!$entity) {
            $entity = $this->viewFactory->create($this->node, $this->request->getPathInfo());
        }

        $entity->addView();

        if ($this->deviceDetector->isDesktop()) {
            $entity->addDesktopView();
        } elseif ($this->deviceDetector->isMobile()) {
            $entity->addMobileView();
        }

        $this->save($entity);
    }

    protected function createReferer()
    {
        if (!$this->request->headers->has('referer')) {
            return;
        }

        $referer = $this->request->headers->get('referer');

        if (!filter_var($referer, FILTER_VALIDATE_URL)) {
            return;
        }

        if (!in_array(parse_url($referer, PHP_URL_SCHEME), ['http', 'https'])) {
            return;
        }

        $entity = $this->refererRepositoryQuery->create()
            ->filterByRequest($this->request)
            ->andWhere('.date=CURRENT_DATE()')
            ->findOne()
        ;

        if (!$entity) {
            $entity = $this->refererFactory->create($this->node, $referer);
        }

        $entity->addView();
        $this->save($entity);
    }

    protected function save(EntityInterface $entity)
    {
        if ($entity->getId()) {
            $this->manager->update($entity);
        } else {
            $this->manager->create($entity);
        }
    }
}
