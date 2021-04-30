<?php

namespace App\Core\Repository\Site\Page;

use App\Core\Repository\RepositoryQuery;
use Knp\Component\Pager\PaginatorInterface;
use App\Core\Entity\Site\Navigation;

/**
 * class PageRepositoryQuery.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class PageRepositoryQuery extends RepositoryQuery
{
    public function __construct(PageRepository $repository, PaginatorInterface $paginator)
    {
        parent::__construct($repository, 'p', $paginator);
    }

    protected function filterHandler(string $name, $value)
    {
        if ($name === 'navigation') {
            return $this->filterByNavigation($value);
        } else {
            return parent::filterHandler($name, $value);
        }
    }

    public function filterByNavigation(Navigation $navigation)
    {
        return $this
            ->leftJoin('.nodes', 'node')
            ->leftJoin('node.menu', 'menu')
            ->leftJoin('menu.navigation', 'navigation')
            ->where('navigation.id = :navigationId')
            ->setParameter(':navigationId', $navigation->getId());
    }

    public function filterById($id)
    {
        $this
            ->where('.id = :id')
            ->setParameter(':id', $id)
        ;

        return $this;
    }
}
