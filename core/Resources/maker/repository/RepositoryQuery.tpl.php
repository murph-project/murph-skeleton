<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use App\Core\Repository\RepositoryQuery;
use Knp\Component\Pager\PaginatorInterface;
use <?= $repository; ?> as Repository;

class <?= $class_name; ?> extends RepositoryQuery
{
    public function __construct(Repository $repository, PaginatorInterface $paginator)
    {
        parent::__construct($repository, '<?= $id; ?>', $paginator);
    }
}
