<?php

namespace App\Core\Controller\Site;

use App\Core\Controller\Admin\AdminController;
use App\Core\Entity\Site\Navigation;
use App\Core\Factory\Site\MenuFactory;
use App\Core\Form\Site\MenuType;
use App\Core\Repository\Site\NavigationRepositoryQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/site/tree")
 */
class TreeAdminController extends AdminController
{
    /**
     * @Route("/", name="admin_site_tree_index")
     */
    public function index(NavigationRepositoryQuery $navigationQuery, Session $session): Response
    {
        $navigation = null;

        if ($session->has('site_tree_last_navigation')) {
            $navigation = $navigationQuery->create()
                ->filterById((int) $session->get('site_tree_last_navigation'))
                ->findOne()
            ;
        }

        if (null === $navigation) {
            $navigation = $navigationQuery->create()
                ->orderBy('.sortOrder')
                ->findOne()
            ;
        }

        if (null === $navigation) {
            $this->addFlash('warning', 'You must add a navigation.');

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
        MenuFactory $menuFactory,
        Session $session
    ): Response {
        $navigations = $navigationQuery->create()
            ->orderBy('.sortOrder')
            ->find()
        ;

        $session->set('site_tree_last_navigation', $navigation->getId());

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
