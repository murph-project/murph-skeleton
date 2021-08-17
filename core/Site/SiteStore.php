<?php

namespace App\Core\Site;

use App\Core\Entity\Site\Navigation;
use App\Core\Entity\Site\Node;
use App\Core\Repository\Site\NavigationRepositoryQuery;

/**
 * class SiteStore.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class SiteStore
{
    protected NavigationRepositoryQuery $navigationRepositoryQuery;
    protected SiteRequest $siteRequest;

    public function __construct(NavigationRepositoryQuery $navigationRepositoryQuery, SiteRequest $siteRequest)
    {
        $this->navigationRepositoryQuery = $navigationRepositoryQuery;
        $this->siteRequest = $siteRequest;
    }

    public function getNavigations(): array
    {
        return $this->navigationRepositoryQuery->create()
            ->orderBy('.sortOrder')
            ->find()
        ;
    }

    public function getNavigation(string $code): ?Navigation
    {
        return $this->navigationRepositoryQuery->create()
            ->where('.code = :code')
            ->setParameter(':code', $code)
            ->findOne()
        ;
    }

    public function isActiveNode(Node $node, $deep = false): bool
    {
        $siteRequestNode = $this->siteRequest->getNode();

        if (!$siteRequestNode) {
            return false;
        }

        if ($node->getRouteName() === $siteRequestNode->getRouteName()) {
            return true;
        }

        if ($deep) {
            foreach ($node->getAllChildren() as $child) {
                if ($child->getRouteName() === $siteRequestNode->getRouteName()) {
                    return true;
                }
            }
        }

        return false;
    }
}
