<?php

namespace App\Core\Repository\Site;

use App\Core\Repository\RepositoryQuery;
use Knp\Component\Pager\PaginatorInterface;

/**
 * class NavigationRepositoryQuery.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class NavigationRepositoryQuery extends RepositoryQuery
{
    public function __construct(NavigationRepository $repository, PaginatorInterface $paginator)
    {
        parent::__construct($repository, 'n', $paginator);
    }

    public function filterById($id)
    {
        $this
            ->where('.id = :id')
            ->setParameter(':id', $id)
        ;

        return $this;
    }

    public function whereDomain($domain)
    {
        return $this
            ->andWhere('.domain = :domain')
            ->setParameter(':domain', $domain)
        ;
    }
}
