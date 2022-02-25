<?php

namespace App\Core\Analytic;

use App\Core\Entity\Site\Node;
use App\Core\Repository\Analytic\RefererRepositoryQuery;
use App\Core\Repository\Analytic\ViewRepositoryQuery;

/**
 * class DateRangeAnalytic.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class DateRangeAnalytic
{
    protected ViewRepositoryQuery $viewQuery;
    protected RefererRepositoryQuery $refererQuery;
    protected ?Node $node;
    protected ?\DateTime $from;
    protected ?\DateTime $to;
    protected bool $reload = true;
    protected array $cache = [];

    public function __construct(ViewRepositoryQuery $viewQuery, RefererRepositoryQuery $refererQuery)
    {
        $this->viewQuery = $viewQuery;
        $this->refererQuery = $refererQuery;
    }

    public function getViews(): array
    {
        $entities = $this->getEntities('view');
        $this->reload = false;

        if ($entities) {
            $first = $entities[0];
            $last = $entities[count($entities) - 1];

            $diff = $first->getDate()->diff($last->getDate());

            if ($diff->days >= 90) {
                $format = 'Y-m';
            } else {
                $format = 'Y-m-d';
            }
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

    public function getPathViews(): array
    {
        $entities = $this->getEntities('view');
        $this->reload = false;

        $datas = [];

        foreach ($entities as $entity) {
            $index = $entity->getPath();

            if (!isset($datas[$index])) {
                $datas[$index] = [
                    'views' => 0,
                    'desktopViews' => 0,
                    'mobileViews' => 0,
                ];
            }

            $datas[$index]['views'] += $entity->getViews();
            $datas[$index]['desktopViews'] += $entity->getDesktopViews();
            $datas[$index]['mobileViews'] += $entity->getMobileViews();
        }

        uasort($datas, function($a, $b) {
            if ($a['views'] > $b['views']) {
                return -1;
            }

            if ($a['views'] < $b['views']) {
                return 1;
            }

            return 0;
        });

        return $datas;
    }

    public function getReferers(): array
    {
        $entities = $this->getEntities('referer');
        $this->reload = false;

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

            if (empty($path)) {
                $path = '/';
            }

            if (!isset($datas[$index]['uris'][$path])) {
                $datas[$index]['uris'][$path] = 0;
            }

            $datas[$index]['uris'][$path] += $entity->getViews();
        }

        uasort($datas, function($a, $b) {
            if ($a['views'] > $b['views']) {
                return -1;
            }

            if ($a['views'] < $b['views']) {
                return 1;
            }

            return 0;
        });

        return $datas;
    }

    public function setDateRange(?\DateTime $from, ?\DateTime $to): self
    {
        $this->from = $from;
        $this->to = $to;
        $this->reload = true;

        return $this;
    }

    public function setNode(?Node $node): self
    {
        $this->node = $node;
        $this->reload = true;

        return $this;
    }

    protected function getEntities(string $type): array
    {
        if ('view' === $type) {
            $query = $this->viewQuery->create();
        } elseif ('referer' === $type) {
            $query = $this->refererQuery->create();
        } else {
            throw new \InvalidArgumentException('Invalid type');
        }

        if (!$this->reload && isset($this->cache[$type])) {
            return $this->cache[$type];
        }

        if (null !== $this->from) {
            $query
                ->andWhere('.date >= :from')
                ->setParameter(':from', $this->from)
            ;
        }

        if (null !== $this->to) {
            $query
                ->andWhere('.date <= :to')
                ->setParameter(':to', $this->to)
            ;
        }

        if (null !== $this->node) {
            $query
                ->andWhere('.node = :node')
                ->setParameter(':node', $this->node->getId())
            ;
        }

        $this->cache[$type] = $query->orderBy('.date')->find();

        return $this->cache[$type];
    }
}
