<?php

namespace App\Core\Repository;

use App\Core\Repository\NodeViewRepository as Repository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class NodeViewRepositoryQuery extends RepositoryQuery
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
