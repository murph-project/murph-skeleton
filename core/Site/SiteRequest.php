<?php

namespace App\Core\Site;

use App\Core\Entity\Site\Menu;
use App\Core\Entity\Site\Navigation;
use App\Core\Entity\Site\Node;
use App\Core\Entity\Site\Page\Page;
use App\Core\Repository\Site\NavigationRepositoryQuery;
use App\Core\Repository\Site\NodeRepository;
use App\Core\Repository\Site\Page\PageRepositoryQuery;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * class SiteRequest.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class SiteRequest
{
    protected RequestStack $requestStack;
    protected NodeRepository $nodeRepository;
    protected NavigationRepositoryQuery $navigationRepositoryQuery;
    protected PageRepositoryQuery $pageRepositoryQuery;

    public function __construct(RequestStack $requestStack, NodeRepository $nodeRepository)
    {
        $this->requestStack = $requestStack;
        $this->nodeRepository = $nodeRepository;
    }

    public function getNode(): ?Node
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request->attributes->has('_node')) {
            return $this->nodeRepository->findOneBy([
                'id' => $request->attributes->get('_node'),
            ]);
        }

        return null;
    }

    public function getPage(): ?Page
    {
        $node = $this->getNode();

        if ($node && $node->getPage()) {
            return $node->getPage();
        }

        return null;
    }

    public function getMenu(): ?Menu
    {
        $node = $this->getNode();

        return null !== $node ? $node->getMenu() : null;
    }

    public function getNavigation(): ?Navigation
    {
        $menu = $this->getMenu();

        return null !== $menu ? $menu->getNavigation() : null;
    }

    public function getDomain(): string
    {
        $host = $this->requestStack->getCurrentRequest()->headers->get('host');

        return preg_replace('/:\d+$/', '', $host);
    }

    public function getUri(): string
    {
        return $this->requestStack->getCurrentRequest()->getUri();
    }
}
