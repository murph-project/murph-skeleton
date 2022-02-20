<?php

namespace App\Core\Controller\Analytic;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Core\Entity\Site\Node;
use App\Core\Analytic\RangeAnalytic;

/**
 * @Route("/admin/analytic")
 */
class AnalyticController extends AbstractController
{
    /**
     * @Route("/stats/{node}/{range}", name="admin_analytic_stats")
     */
    public function stats(Node $node, RangeAnalytic $rangeAnalytic, string $range = '7days'): Response
    {
        if (!in_array($range, ['7days', '30days', '90days', '1year'])) {
            throw $this->createNotFoundException();
        }

        $views = $rangeAnalytic->getViews(new \DateTime('now - '.$range), new \DateTime(), $node);
        $pathViews = $rangeAnalytic->getPathViews(new \DateTime('now - '.$range), new \DateTime(), $node);
        $referers = $rangeAnalytic->getReferers(new \DateTime('now - '.$range), new \DateTime(), $node);

        return $this->render('@Core/analytic/stats.html.twig', [
            'range' => $range,
            'views' => $views,
            'pathViews' => $pathViews,
            'referers' => $referers,
            'node' => $node,
        ]);
    }
}
