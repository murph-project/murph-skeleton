<?php

namespace App\Repository;

use App\Core\Repository\RepositoryQuery;
use Knp\Component\Pager\PaginatorInterface;

/**
 * class UserRepositoryQuery.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class UserRepositoryQuery extends RepositoryQuery
{
    public function __construct(UserRepository $repository, PaginatorInterface $paginator)
    {
        parent::__construct($repository, 'u', $paginator);
    }
}
