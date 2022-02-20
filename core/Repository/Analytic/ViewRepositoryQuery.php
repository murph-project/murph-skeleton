<?php

namespace App\Core\Repository\Analytic;

use App\Core\Repository\Analytic\ViewRepository as Repository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Core\Repository\RepositoryQuery;

class ViewRepositoryQuery extends RepositoryQuery
{
    public function __construct(Repository $repository, PaginatorInterface $paginator)
    {
        parent::__construct($repository, 'n', $paginator);
    }

    public function filterByRequest(Request $request)
    {
        return $this
            ->andWhere('.node = :node')
            ->andWhere('.path = :path')
            ->setParameters([
                ':node' => $request->attributes->get('_node'),
                ':path' => $request->getPathInfo(),
            ])
        ;
    }
}
