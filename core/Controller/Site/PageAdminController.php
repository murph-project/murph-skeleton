<?php

namespace App\Core\Controller\Site;

use App\Core\Controller\Admin\Crud\CrudController;
use App\Core\Crud\CrudConfiguration;
use App\Core\Crud\Field;
use App\Core\Entity\Site\Page\Page as Entity;
use App\Core\Form\Site\Page\Filter\PageFilterType as FilterType;
use App\Core\Form\Site\Page\PageType as Type;
use App\Core\Manager\EntityManager;
use App\Core\Repository\Site\Page\PageRepositoryQuery as RepositoryQuery;
use App\Core\Site\PageLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use App\Core\Event\Page\PageEditEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Core\Entity\EntityInterface;

class PageAdminController extends CrudController
{
    /**
     * @Route("/admin/site/page/{page}", name="admin_site_page_index", methods={"GET"}, requirements={"page":"\d+"})
     */
    public function index(RepositoryQuery $query, Request $request, Session $session, int $page = 1): Response
    {
        return $this->doIndex($page, $query, $request, $session);
    }

    /**
     * @Route("/admin/site/page/show/{entity}", name="admin_site_page_show", methods={"GET"})
     */
    public function show(Entity $entity): Response
    {
        return $this->doShow($entity);
    }

    /**
     * @Route("/admin/site/page/filter", name="admin_site_page_filter", methods={"GET"})
     */
    public function filter(Session $session): Response
    {
        return $this->doFilter($session);
    }

    /**
     * @Route("/admin/site/page/edit/{entity}", name="admin_site_page_edit", methods={"GET", "POST"})
     */
    public function edit(
        int $entity,
        EntityManager $entityManager,
        RepositoryQuery $repositoryQuery,
        PageLocator $pageLocator,
        EventDispatcherInterface $eventDispatcher,
        Request $request
    ): Response {
        $entity = $repositoryQuery->filterById($entity)->findOne();

        $event = new PageEditEvent($entity);
        $eventDispatcher->dispatch($event, PageEditEvent::FORM_INIT_EVENT);

        $this->getConfiguration()->setFormOptions('edit', [
            'page_configuration' => $pageLocator->getPage(get_class($entity)),
            'page_builder_options' => $event->getPageBuilderOptions(),
        ]);

        return $this->doEdit($entity, $entityManager, $request);
    }

    /**
     * @Route("/admin/site/page/delete/{entity}", name="admin_site_page_delete", methods={"DELETE"})
     */
    public function delete(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        return $this->doDelete($entity, $entityManager, $request);
    }

    /**
     * @Route("/admin/site/page/batch/{page}", name="admin_site_page_batch", methods={"POST"}, requirements={"page":"\d+"})
     */
    public function batch(RepositoryQuery $query, EntityManager $entityManager, Request $request, Session $session, int $page = 1): Response
    {
        return $this->doBatch($page, $query, $entityManager, $request, $session);
    }

    protected function getConfiguration(): CrudConfiguration
    {
        return CrudConfiguration::create()
            ->setPageTitle('index', 'Pages')
            ->setPageTitle('edit', '{name}')
            ->setPageTitle('show', '{name}')

            ->setPageRoute('index', 'admin_site_page_index')
            ->setPageRoute('edit', 'admin_site_page_edit')
            ->setPageRoute('delete', 'admin_site_page_delete')
            ->setPageRoute('filter', 'admin_site_page_filter')
            ->setPageRoute('batch', 'admin_site_page_batch')

            ->setForm('edit', Type::class, [])
            ->setForm('filter', FilterType::class)
            ->setView('form', '@Core/site/page_admin/_form.html.twig')

            ->setAction('index', 'new', false)
            ->setAction('index', 'show', false)
            ->setAction('edit', 'show', false)

            ->setField('index', 'Name', Field\TextField::class, [
                'property' => 'name',
                'sort' => ['name', '.name'],
                'attr' => ['class' => 'col-4'],
            ])
            ->setField('index', 'Elements', Field\TextField::class, [
                'view' => '@Core/site/page_admin/fields/nodes.html.twig',
                'sort' => ['navigation', function (RepositoryQuery $query, $direction) {
                    $query
                        ->leftJoin('.nodes', 'node')
                        ->leftJoin('node.menu', 'menu')
                        ->leftJoin('menu.navigation', 'navigation')
                        ->orderBy('navigation.label', $direction)
                    ;
                }],
                'attr' => ['class' => 'col-6'],
            ])
            ->setBatchAction('index', 'delete', 'Delete', function(EntityInterface $entity, EntityManager $manager) {
                $manager->delete($entity);
            })
        ;
    }

    protected function getSection(): string
    {
        return 'site_page';
    }
}
