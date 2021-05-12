<?php

namespace App\Core\Controller\Site;

use App\Core\Controller\Admin\AdminController;
use App\Core\Entity\Site\Page\Page as Entity;
use App\Core\Factory\Site\Page\PageFactory as EntityFactory;
use App\Core\Form\Site\Page\Filter\PageFilterType as FilterType;
use App\Core\Form\Site\Page\PageType as EntityType;
use App\Core\Manager\EntityManager;
use App\Core\Repository\Site\Page\PageRepositoryQuery as RepositoryQuery;
use App\Core\Site\PageLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/site/page")
 */
class PageAdminController extends AdminController
{
    /**
     * @Route("/{page}", name="admin_site_page_index", requirements={"page": "\d+"})
     */
    public function index(int $page = 1, RepositoryQuery $query, Request $request, Session $session): Response
    {
        $this->updateFilters($request, $session);

        $pager = $query
            ->useFilters($this->filters)
            ->paginate($page)
        ;

        return $this->render('@Core/site/page_admin/index.html.twig', [
            'pager' => $pager,
            'hasFilters' => !empty($this->filters),
        ]);
    }

    /**
     * @Route("/edit/{entity}", name="admin_site_page_edit")
     */
    public function edit(
        int $entity,
        EntityFactory $factory,
        EntityManager $entityManager,
        RepositoryQuery $repositoryQuery,
        PageLocator $pageLocator,
        Request $request
    ): Response {
        $entity = $repositoryQuery->filterById($entity)->findOne();
        $form = $this->createForm(EntityType::class, $entity, [
            'pageConfiguration' => $pageLocator->getPage(get_class($entity)),
        ]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager->update($entity);

                $this->addFlash('success', 'The data has been saved.');

                return $this->redirectToRoute('admin_site_page_edit', [
                    'entity' => $entity->getId(),
                ]);
            }

            $this->addFlash('warning', 'The form is not valid.');
        }

        return $this->render('@Core/site/page_admin/edit.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/delete/{entity}", name="admin_site_page_delete", methods={"DELETE"})
     */
    public function delete(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->request->get('_token'))) {
            $entityManager->delete($entity);

            $this->addFlash('success', 'The data has been removed.');
        }

        return $this->redirectToRoute('admin_site_page_index');
    }

    /**
     * @Route("/filters", name="admin_site_page_filters")
     */
    public function filters(Session $session): Response
    {
        $form = $this->createForm(FilterType::class);
        $form->submit($session->get('page_filter', []));

        return $this->render('@Core/site/page_admin/filters.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function getSection(): string
    {
        return 'site_page';
    }

    protected function updateFilters(Request $request, Session $session)
    {
        if ($request->query->has('page_filter')) {
            $filters = $request->query->get('page_filter');

            if ('0' === $filters) {
                $filters = [];
            }
        } elseif ($session->has('page_filter')) {
            $filters = $session->get('page_filter');
        } else {
            $filters = [];
        }

        $form = $this->createForm(FilterType::class);
        $form->submit($filters);

        if (empty($filters)) {
            $this->filters = $filters;
            $session->set('page_filter', $filters);
        } elseif ($form->isValid()) {
            $this->filters = $form->getData();
            $session->set('page_filter', $filters);
        }
    }
}
