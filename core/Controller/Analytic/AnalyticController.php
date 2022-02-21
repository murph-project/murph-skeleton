<?php

namespace App\Core\Controller\Analytic;

use App\Core\Analytic\DateRangeAnalytic;
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
    public function stats(Node $node, DateRangeAnalytic $analytic, string $range = '7days'): Response
    {
        if (!in_array($range, ['7days', '30days', '90days', '1year'])) {
            throw $this->createNotFoundException();
        }

        $analytic
            ->setDateRange(new \DateTime('now - '.$range), new \DateTime())
            ->setNode($node)
        ;

        return $this->render('@Core/analytic/stats.html.twig', [
            'range' => $range,
            'views' => $analytic->getViews(),
            'pathViews' => $analytic->getPathViews(),
            'referers' => $analytic->getReferers(),
            'node' => $node,
        ]);
    }
}
