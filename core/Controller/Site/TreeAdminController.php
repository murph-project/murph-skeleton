<?php

namespace App\Core\Controller\Site;

use App\Core\Controller\Admin\AdminController;
use App\Core\Entity\Site\Navigation;
use App\Core\Factory\Site\MenuFactory;
use App\Core\Form\Site\MenuType;
use App\Core\Repository\Site\NavigationRepositoryQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/site/tree")
 */
class TreeAdminController extends AdminController
{
    /**
     * @Route("/", name="admin_site_tree_index")
     */
    public function index(NavigationRepositoryQuery $navigationQuery): Response
    {
        $navigation = $navigationQuery->create()->findOne();

        if (null === $navigation) {
            $this->addFlash('warning', 'Vous devez ajouter une navigation.');

            return $this->redirectToRoute('admin_site_navigation_new');
        }

        return $this->redirectToRoute('admin_site_tree_navigation', [
            'navigation' => $navigation->getId(),
        ]);
    }

    /**
     * @Route("/navigation/{navigation}", name="admin_site_tree_navigation")
     */
    public function navigation(
        Navigation $navigation,
        NavigationRepositoryQuery $navigationQuery,
        MenuFactory $menuFactory
    ): Response {
        $navigations = $navigationQuery->create()->find();

        $forms = [
            'menu' => $this->createForm(MenuType::class, $menuFactory->create())->createView(),
            'menus' => [],
        ];

        foreach ($navigation->getMenus() as $menu) {
            $forms['menus'][$menu->getId()] = $this->createForm(MenuType::class, $menu)->createView();
        }

        return $this->render('@Core/site/tree_admin/navigation.html.twig', [
            'navigation' => $navigation,
            'navigations' => $navigations,
            'forms' => $forms,
        ]);
    }

    public function getSection(): string
    {
        return 'site_tree';
    }
}
