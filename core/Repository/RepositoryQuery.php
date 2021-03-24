<?php

namespace App\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;

/**
 * class RepositoryQuery.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
abstract class RepositoryQuery
{
    protected ServiceEntityRepository $repository;
    protected QueryBuilder $query;
    protected PaginatorInterface $paginator;
    protected string $id;

    public function __construct(ServiceEntityRepository $repository, string $id, PaginatorInterface $paginator = null)
    {
        $this->repository = $repository;
        $this->query = $repository->createQueryBuilder($id);
        $this->paginator = $paginator;
        $this->id = $id;
    }

    public function __call(string $name, $params): self
    {
        $fn = function (&$data) {
            if (is_string($data)) {
                $words = explode(' ', $data);

                foreach ($words as $k => $v) {
                    if (isset($v[0]) && '.' === $v[0]) {
                        $words[$k] = $this->id.$v;
                    }
                }

                $data = implode(' ', $words);
            } elseif (is_array($data)) {
                foreach ($data as $k => $v) {
                    $fn($data[$k]);
                }
            }

            return $data;
        };

        foreach ($params as $key => $value) {
            $fn($params[$key]);
        }

        call_user_func_array([$this->query, $name], $params);

        return $this;
    }

    public function create()
    {
        $class = get_called_class();

        return new $class($this->repository, $this->paginator);
    }

    public function call(callable $fn): self
    {
        $fn($this->query, $this);

        return $this;
    }

    public function findOne()
    {
        return $this->query->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function find()
    {
        return $this->query->getQuery()->getResult();
    }

    public function paginate(int $page = 1, int $limit = 20)
    {
        return $this->paginator->paginate($this->query->getQuery(), $page, $limit);
    }

    public function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
