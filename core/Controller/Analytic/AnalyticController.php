<?php

namespace App\Core\Controller\Analytic;

use App\Core\Analytic\RangeAnalytic;
use App\Core\Entity\Site\Node;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

        $rangeAnalytic
            ->setDateRange(new \DateTime('now - '.$range), new \DateTime())
            ->setNode($node)
        ;

        return $this->render('@Core/analytic/stats.html.twig', [
            'range' => $range,
            'views' => $rangeAnalytic->getViews(),
            'pathViews' => $rangeAnalytic->getPathViews(),
            'referers' => $rangeAnalytic->getReferers(),
            'node' => $node,
        ]);
    }
}
