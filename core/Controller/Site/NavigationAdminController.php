<?php

namespace App\Core\Controller\Site;

use App\Core\Controller\Admin\AdminController;
use App\Core\Entity\Site\Navigation as Entity;
use App\Core\Factory\Site\NavigationFactory as EntityFactory;
use App\Core\Form\Site\NavigationType as EntityType;
use App\Core\Manager\EntityManager;
use App\Core\Repository\Site\NavigationRepositoryQuery as RepositoryQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/site/navigation")
 */
class NavigationAdminController extends AdminController
{
    /**
     * @Route("/{page}", name="admin_site_navigation_index", requirements={"page": "\d+"})
     */
    public function index(int $page = 1, RepositoryQuery $query, Request $request): Response
    {
        $pager = $query->paginate($page);

        return $this->render('@Core/site/navigation_admin/index.html.twig', [
            'pager' => $pager,
        ]);
    }

    /**
     * @Route("/new", name="admin_site_navigation_new")
     */
    public function new(EntityFactory $factory, EntityManager $entityManager, Request $request): Response
    {
        $entity = $factory->create();
        $form = $this->createForm(EntityType::class, $entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager->create($entity);
                $this->addFlash('success', 'Donnée enregistrée.');

                return $this->redirectToRoute('admin_site_navigation_edit', [
                    'entity' => $entity->getId(),
                ]);
            }
            $this->addFlash('warning', 'Le formulaire est invalide.');
        }

        return $this->render('@Core/site/navigation_admin/new.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/edit/{entity}", name="admin_site_navigation_edit")
     */
    public function edit(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        $form = $this->createForm(EntityType::class, $entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager->update($entity);
                $this->addFlash('success', 'Donnée enregistrée.');

                return $this->redirectToRoute('admin_site_navigation_edit', [
                    'entity' => $entity->getId(),
                ]);
            }

            $this->addFlash('warning', 'Le formulaire est invalide.');
        }

        return $this->render('@Core/site/navigation_admin/edit.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/show/{entity}", name="admin_site_navigation_show")
     */
    public function show(Entity $entity): Response
    {
        return $this->render('@Core/site/navigation_admin/show.html.twig', [
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/delete/{entity}", name="admin_site_navigation_delete", methods={"DELETE"})
     */
    public function delete(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->request->get('_token'))) {
            $entityManager->delete($entity);

            $this->addFlash('success', 'Données supprimée..');
        }

        return $this->redirectToRoute('admin_site_navigation_index');
    }

    public function getSection(): string
    {
        return 'site_navigation';
    }
}
