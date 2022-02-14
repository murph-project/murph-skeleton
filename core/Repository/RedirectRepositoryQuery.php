<?php

namespace App\Core\Repository;

use App\Core\Repository\RepositoryQuery;
use Knp\Component\Pager\PaginatorInterface;
use App\Core\Repository\RedirectRepository as Repository;

class RedirectRepositoryQuery extends RepositoryQuery
{
    public function __construct(Repository $repository, PaginatorInterface $paginator)
    {
        parent::__construct($repository, 'r', $paginator);
    }
}
