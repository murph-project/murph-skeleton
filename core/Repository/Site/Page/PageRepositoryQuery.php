<?php

namespace App\Core\Repository\Site\Page;

use App\Core\Entity\Site\Navigation;
use App\Core\Repository\RepositoryQuery;
use Knp\Component\Pager\PaginatorInterface;

/**
 * class PageRepositoryQuery.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class PageRepositoryQuery extends RepositoryQuery
{
    public function __construct(PageRepository $repository, PaginatorInterface $paginator)
    {
        parent::__construct($repository, 'p', $paginator);

        $this->forcedFilterHandlers[] = 'isAssociated';
    }

    public function filterByNavigation(Navigation $navigation): self
    {
        return $this
            ->leftJoin('.nodes', 'node')
            ->leftJoin('node.menu', 'menu')
            ->leftJoin('menu.navigation', 'navigation')
            ->where('navigation.id = :navigationId')
            ->setParameter(':navigationId', $navigation->getId())
        ;
    }

    public function filterById($id): self
    {
        $this
            ->where('.id = :id')
            ->setParameter(':id', $id)
        ;

        return $this;
    }

    protected function withAssociation(bool $isAssociated): self
    {
        $entities = $this->create()->find();
        $ids = [];

        foreach ($entities as $entity) {
            if ($isAssociated && !$entity->getNodes()->isEmpty()) {
                $ids[] = $entity->getId();
            } elseif (!$isAssociated && $entity->getNodes()->isEmpty()) {
                $ids[] = $entity->getId();
            }
        }

        $this
            ->andWhere('.id IN (:ids)')
            ->setParameter(':ids', $ids)
        ;

        return $this;
    }

    protected function filterHandler(string $name, $value)
    {
        if ('navigation' === $name) {
            return $this->filterByNavigation($value);
        }

        if ('isAssociated' === $name && $value > -1) {
            $this->withAssociation((bool) $value);
        }

        return parent::filterHandler($name, $value);
    }
}
