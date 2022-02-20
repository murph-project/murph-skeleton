<?php

namespace App\Core\Analytic;

use App\Core\Repository\Analytic\RefererRepositoryQuery;
use App\Core\Repository\Analytic\ViewRepositoryQuery;
use App\Core\Entity\Site\Node;

/**
 * class RangeAnalytic.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class RangeAnalytic
{
    protected ViewRepositoryQuery $viewQuery;
    protected RefererRepositoryQuery $refererQuery;

    public function __construct(ViewRepositoryQuery $viewQuery, RefererRepositoryQuery $refererQuery)
    {
        $this->viewQuery = $viewQuery;
        $this->refererQuery = $refererQuery;
    }

    public function getViews(\DateTime $from, \DateTime $to, Node $node): array
    {
        $entities = $this->viewQuery->create()
            ->andWhere('.date >= :from')
            ->andWhere('.date <= :to')
            ->andWhere('.node = :node')
            ->orderBy('.date')
            ->setParameters([
                ':from' => $from,
                ':to' => $to,
                ':node' => $node->getId(),
            ])
            ->find()
        ;

        $diff = $from->diff($to);

        if ($diff->days >= 365) {
            $format = 'Y-m';
        } else {
            $format = 'Y-m-d';
        }

        $datas = [];

        foreach ($entities as $entity) {
            $index = $entity->getDate()->format($format);

            if (!isset($datas[$index])) {
                $datas[$index] = 0;
            }

            $datas[$index] += $entity->getViews();
        }

        return $datas;
    }

    public function getPathViews(\DateTime $from, \DateTime $to, Node $node): array
    {
        $entities = $this->viewQuery->create()
            ->andWhere('.date >= :from')
            ->andWhere('.date <= :to')
            ->andWhere('.node = :node')
            ->orderBy('.date')
            ->setParameters([
                ':from' => $from,
                ':to' => $to,
                ':node' => $node->getId(),
            ])
            ->find()
        ;

        $datas = [];

        foreach ($entities as $entity) {
            $index = $entity->getPath();

            if (!isset($datas[$index])) {
                $datas[$index] = 0;
            }

            $datas[$index] += $entity->getViews();
        }

        return $datas;
    }

    public function getReferers(\DateTime $from, \DateTime $to, Node $node): array
    {
        $entities = $this->refererQuery->create()
            ->andWhere('.date >= :from')
            ->andWhere('.date <= :to')
            ->andWhere('.node = :node')
            ->orderBy('.date')
            ->setParameters([
                ':from' => $from,
                ':to' => $to,
                ':node' => $node->getId(),
            ])
            ->find()
        ;

        $datas = [];

        foreach ($entities as $entity) {
            $index = parse_url($entity->getUri(), PHP_URL_HOST);

            if (!isset($datas[$index])) {
                $datas[$index] = [
                    'views' => 0,
                    'uris' => [],
                ];
            }

            $datas[$index]['views'] += $entity->getViews();

            $path = parse_url($entity->getUri(), PHP_URL_PATH);

            if (!isset($datas[$index]['uris'][$path])) {
                $datas[$index]['uris'][$path] = 0;
            }

            $datas[$index]['uris'][$path] += $entity->getViews();
        }

        return $datas;
    }
}
