<?php

namespace App\Core\Repository\Site\Page;

use App\Core\Repository\RepositoryQuery;
use Knp\Component\Pager\PaginatorInterface;

/**
 * class BlockRepositoryQuery.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class BlockRepositoryQuery extends RepositoryQuery
{
    public function __construct(BlockRepository $repository, PaginatorInterface $paginator)
    {
        parent::__construct($repository, 'b', $paginator);
    }
}
