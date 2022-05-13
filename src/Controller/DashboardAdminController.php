<?php

namespace App\Controller;

use App\Core\Controller\Dashboard\DashboardAdminController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class DashboardAdminController extends Controller
{
    /**
     * @Route("/", name="admin_dashboard_index")
     */
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }
}
