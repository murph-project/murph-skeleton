<?php

namespace App\Core\Controller\Site;

use App\Core\Controller\Admin\Crud\CrudController;
use App\Core\Crud\CrudConfiguration;
use App\Core\Crud\Field;
use App\Core\Entity\Site\Navigation as Entity;
use App\Core\Event\Setting\NavigationSettingEvent;
use App\Core\Factory\Site\NavigationFactory as Factory;
use App\Core\Form\Site\NavigationType as Type;
use App\Core\Manager\EntityManager;
use App\Core\Repository\NavigationSettingRepositoryQuery;
use App\Core\Repository\Site\NavigationRepositoryQuery as RepositoryQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class NavigationAdminController extends CrudController
{
    /**
     * @Route("/admin/site/navigation/{page}", name="admin_site_navigation_index", methods={"GET"}, requirements={"page":"\d+"})
     */
    public function index(RepositoryQuery $query, Request $request, Session $session, int $page = 1): Response
    {
        return $this->doIndex($page, $query, $request, $session);
    }

    /**
     * @Route("/admin/site/navigation/new", name="admin_site_navigation_new", methods={"GET", "POST"})
     */
    public function new(Factory $factory, EntityManager $entityManager, Request $request): Response
    {
        return $this->doNew($factory->create(), $entityManager, $request);
    }

    /**
     * @Route("/admin/site/navigation/show/{entity}", name="admin_site_navigation_show", methods={"GET"})
     */
    public function show(
        Entity $entity,
        EventDispatcherInterface $eventDispatcher,
        NavigationSettingRepositoryQuery $settingQuery
    ): Response {
        $eventDispatcher->dispatch(new NavigationSettingEvent([
            'navigation' => $entity,
        ]), NavigationSettingEvent::INIT_EVENT);

        $settings = $settingQuery
            ->where('.navigation = :navigation')
            ->orderBy('.section, .label')
            ->setParameter(':navigation', $entity->getId())
            ->paginate(1, 1000)
        ;

        $this->getConfiguration()->addViewData('show', 'settings', $settings);

        return $this->doShow($entity);
    }

    /**
     * @Route("/admin/site/navigation/filter", name="admin_site_navigation_filter", methods={"GET"})
     */
    public function filter(Session $session): Response
    {
        return $this->doFilter($session);
    }

    /**
     * @Route("/admin/site/navigation/edit/{entity}", name="admin_site_navigation_edit", methods={"GET", "POST"})
     */
    public function edit(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        return $this->doEdit($entity, $entityManager, $request);
    }

    /**
     * @Route("/admin/site/navigation/sort/{page}", name="admin_site_navigation_sort", methods={"POST"}, requirements={"page":"\d+"})
     */
    public function sort(RepositoryQuery $query, EntityManager $entityManager, Request $request, Session $session, int $page = 1, ): Response
    {
        return $this->doSort($page, $query, $entityManager, $request, $session);
    }

    /**
     * @Route("/admin/site/navigation/delete/{entity}", name="admin_site_navigation_delete", methods={"DELETE"})
     */
    public function delete(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        return $this->doDelete($entity, $entityManager, $request);
    }

    protected function getConfiguration(): CrudConfiguration
    {
        return CrudConfiguration::create()
            ->setPageTitle('index', 'Navigations')
            ->setPageTitle('edit', '{label}')
            ->setPageTitle('new', 'New navigation')
            ->setPageTitle('show', '{label}')

            ->setPageRoute('index', 'admin_site_navigation_index')
            ->setPageRoute('new', 'admin_site_navigation_new')
            ->setPageRoute('edit', 'admin_site_navigation_edit')
            ->setPageRoute('show', 'admin_site_navigation_show')
            ->setPageRoute('sort', 'admin_site_navigation_sort')
            ->setPageRoute('delete', 'admin_site_navigation_delete')
            ->setPageRoute('filter', 'admin_site_navigation_filter')
            ->setPageRoute('redirects', 'admin_redirect_index')

            ->setForm('edit', Type::class, [])
            ->setForm('new', Type::class)

            ->setView('index', '@Core/site/navigation_admin/index.html.twig')
            ->setView('show', '@Core/site/navigation_admin/show.html.twig')
            ->setView('show_entity', '@Core/site/navigation_admin/_show.html.twig')
            ->setView('form', '@Core/site/navigation_admin/_form.html.twig')

            ->setIsSortableCollection('index', true)

            ->setField('index', 'Label', Field\TextField::class, [
                'property' => 'label',
                'attr' => ['class' => 'miw-200'],
            ])
            ->setField('index', 'Domain', Field\ButtonField::class, [
                'property' => 'domain',
                'button_attr' => ['class' => 'btn btn-light'],
                'attr' => ['class' => 'miw-200'],
            ])
            ->setField('index', 'Locale', Field\ButtonField::class, [
                'property' => 'locale',
                'button_attr' => ['class' => 'btn btn-light'],
            ])
        ;
    }

    protected function getSection(): string
    {
        return 'site_navigation';
    }
}
