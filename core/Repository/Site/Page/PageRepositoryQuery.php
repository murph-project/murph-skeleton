<?php

namespace App\Core\Repository\Site\Page;

use App\Core\Repository\RepositoryQuery;
use Knp\Component\Pager\PaginatorInterface;

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

    public function filterById($id)
    {
        $this
            ->where('.id = :id')
            ->setParameter(':id', $id)
        ;

        return $this;
    }
}
