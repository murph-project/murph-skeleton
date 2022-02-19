<?php

namespace App\Core\Repository;

use App\Core\Entity\NodeView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NodeView|null find($id, $lockMode = null, $lockVersion = null)
 * @method NodeView|null findOneBy(array $criteria, array $orderBy = null)
 * @method NodeView[]    findAll()
 * @method NodeView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NodeViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NodeView::class);
    }
}
