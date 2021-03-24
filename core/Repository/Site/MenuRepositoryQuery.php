<?php

namespace App\Core\Repository\Site;

use App\Core\Repository\RepositoryQuery;
use Knp\Component\Pager\PaginatorInterface;

/**
 * class MenuRepositoryQuery.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class MenuRepositoryQuery extends RepositoryQuery
{
    public function __construct(MenuRepository $repository, PaginatorInterface $paginator)
    {
        parent::__construct($repository, 'm', $paginator);
    }
}
