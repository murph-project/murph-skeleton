<?php

namespace App\Core\Controller\Site;

use App\Core\Controller\Admin\AdminController;
use App\Core\Entity\Site\Menu as Entity;
use App\Core\Entity\Site\Navigation;
use App\Core\Factory\Site\MenuFactory as EntityFactory;
use App\Core\Form\Site\MenuType as EntityType;
use App\Core\Manager\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/site/menu")
 */
class MenuAdminController extends AdminController
{
    /**
     * @Route("/new/{navigation}", name="admin_site_menu_new", methods={"POST"})
     */
    public function new(Navigation $navigation, EntityFactory $factory, EntityManager $entityManager, Request $request): Response
    {
        $entity = $factory->create($navigation);
        $form = $this->createForm(EntityType::class, $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager->create($entity);

            $this->addFlash('success', 'The data has been saved.');
        } else {
            $this->addFlash('warning', 'The form is not valid.');
        }

        return $this->redirectToRoute('admin_site_tree_navigation', [
            'navigation' => $navigation->getId(),
        ]);
    }

    /**
     * @Route("/edit/{entity}", name="admin_site_menu_edit", methods={"POST"})
     */
    public function edit(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        $form = $this->createForm(EntityType::class, $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager->update($entity);
            $this->addFlash('success', 'The data has been saved.');
        } else {
            $this->addFlash('warning', 'The form is not valid.');
        }

        return $this->redirectToRoute('admin_site_tree_navigation', [
            'navigation' => $entity->getNavigation()->getId(),
        ]);
    }

    /**
     * @Route("/delete/{entity}", name="admin_site_menu_delete", methods={"DELETE"})
     */
    public function delete(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->request->get('_token'))) {
            $entityManager->delete($entity);

            $this->addFlash('success', 'The data has been removed.');
        }

        return $this->redirectToRoute('admin_site_tree_navigation', [
            'navigation' => $entity->getNavigation()->getId(),
        ]);
    }

    public function getSection(): string
    {
        return '';
    }
}
