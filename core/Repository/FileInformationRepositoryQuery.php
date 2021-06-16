<?php

namespace App\Core\Repository;

use App\Core\Repository\FileInformationRepository as Repository;
use Knp\Component\Pager\PaginatorInterface;

class FileInformationRepositoryQuery extends RepositoryQuery
{
    public function __construct(Repository $repository, PaginatorInterface $paginator)
    {
        parent::__construct($repository, 'f', $paginator);
    }
}
