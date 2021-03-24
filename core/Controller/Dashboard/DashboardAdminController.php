<?php

namespace App\Core\Controller\Dashboard;

use App\Core\Controller\Admin\AdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class DashboardAdminController extends AdminController
{
    /**
     * @Route("/", name="admin_dashboard_index")
     */
    public function index(): Response
    {
        return $this->render('@Core/dashboard/index.html.twig', [
        ]);
    }

    protected function getSection(): string
    {
        return 'dashboard';
    }
}
