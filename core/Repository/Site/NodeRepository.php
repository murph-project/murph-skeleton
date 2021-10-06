<?php

namespace App\Core\Repository\Site;

use App\Core\Entity\Site\Node;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class NodeRepository extends NestedTreeRepository
{
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager, $manager->getClassMetadata(Node::class));
    }

    public function urlExists($url, Node $node): bool
    {
        $query = $this->createQueryBuilder('n')
            ->join('n.menu', 'm')
            ->where('n.url = :url')
            ->andWhere('n.disableUrl = 0')
            ->andWhere('n.aliasNode is null')
            ->andWhere('m.navigation = :navigation')
            ->setParameter(':url', $url)
            ->setParameter(':navigation', $node->getMenu()->getNavigation())
        ;

        if ($node->getId()) {
            $query
                ->andWhere('n.id != :id')
                ->setParameter(':id', $node->getId())
            ;
        }

        return $query->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult() !== null
        ;
    }
}
