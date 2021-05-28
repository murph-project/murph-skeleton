<?php

namespace App\Core\Repository;

use Knp\Component\Pager\PaginatorInterface;

/**
 * class NavigationSettingRepositoryQuery.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class NavigationSettingRepositoryQuery extends RepositoryQuery
{
    public function __construct(NavigationSettingRepository $repository, PaginatorInterface $paginator)
    {
        parent::__construct($repository, 'ns', $paginator);
    }
}
