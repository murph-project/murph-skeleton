<?php

namespace App\Core\Repository;

use Knp\Component\Pager\PaginatorInterface;

/**
 * class SettingRepositoryQuery.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class SettingRepositoryQuery extends RepositoryQuery
{
    public function __construct(SettingRepository $repository, PaginatorInterface $paginator)
    {
        parent::__construct($repository, 's', $paginator);
    }
}
