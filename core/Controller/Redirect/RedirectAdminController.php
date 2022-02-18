<?php

namespace App\Core\Controller\Redirect;

use App\Core\Controller\Admin\Crud\CrudController;
use App\Core\Crud\CrudConfiguration;
use App\Core\Crud\Field;
use App\Core\Entity\EntityInterface;
use App\Core\Entity\Redirect as Entity;
use App\Core\Factory\RedirectFactory as Factory;
use App\Core\Form\Filter\RedirectFilterType as FilterType;
use App\Core\Form\RedirectType as Type;
use App\Core\Manager\EntityManager;
use App\Core\Repository\RedirectRepositoryQuery as RepositoryQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class RedirectAdminController extends CrudController
{
    /**
     * @Route("/admin/redirect/{page}", name="admin_redirect_index", methods={"GET"}, requirements={"page":"\d+"})
     */
    public function index(RepositoryQuery $query, Request $request, Session $session, int $page = 1): Response
    {
        return $this->doIndex($page, $query, $request, $session);
    }

    /**
     * @Route("/admin/redirect/new", name="admin_redirect_new", methods={"GET", "POST"})
     */
    public function new(Factory $factory, EntityManager $entityManager, Request $request): Response
    {
        return $this->doNew($factory->create(), $entityManager, $request);
    }

    /**
     * @Route("/admin/redirect/show/{entity}", name="admin_redirect_show", methods={"GET"})
     */
    public function show(Entity $entity): Response
    {
        return $this->doShow($entity);
    }

    /**
     * @Route("/admin/redirect/filter", name="admin_redirect_filter", methods={"GET"})
     */
    public function filter(Session $session): Response
    {
        return $this->doFilter($session);
    }

    /**
     * @Route("/admin/redirect/edit/{entity}", name="admin_redirect_edit", methods={"GET", "POST"})
     */
    public function edit(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        return $this->doEdit($entity, $entityManager, $request);
    }

    /**
     * @Route("/admin/redirect/sort/{page}", name="admin_redirect_sort", methods={"POST"}, requirements={"page":"\d+"})
     */
    public function sort(RepositoryQuery $query, EntityManager $entityManager, Request $request, Session $session, int $page = 1): Response
    {
        return $this->doSort($page, $query, $entityManager, $request, $session);
    }

    /**
     * @Route("/admin/redirect/batch/{page}", name="admin_redirect_batch", methods={"POST"}, requirements={"page":"\d+"})
     */
    public function batch(RepositoryQuery $query, EntityManager $entityManager, Request $request, Session $session, int $page = 1): Response
    {
        return $this->doBatch($page, $query, $entityManager, $request, $session);
    }

    /**
     * @Route("/admin/redirect/delete/{entity}", name="admin_redirect_delete", methods={"DELETE"})
     */
    public function delete(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        return $this->doDelete($entity, $entityManager, $request);
    }

    protected function getConfiguration(): CrudConfiguration
    {
        return CrudConfiguration::create()
            ->setPageTitle('index', 'Redirects')
            ->setPageTitle('edit', '{label}')
            ->setPageTitle('new', 'New redirect')

            ->setPageRoute('index', 'admin_redirect_index')
            ->setPageRoute('new', 'admin_redirect_new')
            ->setPageRoute('edit', 'admin_redirect_edit')
            ->setPageRoute('sort', 'admin_redirect_sort')
            ->setPageRoute('batch', 'admin_redirect_batch')
            ->setPageRoute('delete', 'admin_redirect_delete')
            ->setPageRoute('filter', 'admin_redirect_filter')

            ->setForm('edit', Type::class, [])
            ->setForm('new', Type::class)
            ->setForm('filter', FilterType::class)

            ->setView('form', '@Core/redirect/redirect_admin/_form.html.twig')
            ->setMaxPerPage('index', 100)
            ->setIsSortableCollection('index', true)

            ->setAction('index', 'show', false)
            ->setAction('edit', 'show', false)

            ->setField('index', 'Label', Field\TextField::class, [
                'property' => 'label',
                'attr' => ['class' => 'col-4'],
            ])
            ->setField('index', 'Rule', Field\TextField::class, [
                'view' => '@Core/redirect/redirect_admin/field/rule.html.twig',
                'attr' => ['class' => 'col-6'],
            ])
            ->setField('index', 'Enabled', Field\ButtonField::class, [
                'property_builder' => function(EntityInterface $entity) {
                    return $entity->getIsEnabled() ? 'Yes' : 'No';
                },
                'attr' => ['class' => 'col-1'],
                'button_attr_builder' => function(EntityInterface $entity) {
                    return ['class' => 'btn btn-sm btn-'.($entity->getIsEnabled() ? 'success' : 'primary')];
                },
            ])
            ->setField('index', 'Type', Field\ButtonField::class, [
                'property' => 'redirectCode',
                'attr' => ['class' => 'col-1'],
                'button_attr' => ['class' => 'btn btn-sm btn-light border-secondary font-weight-bold'],
            ])
            ->setBatchAction('index', 'enable', 'Enable', function (EntityInterface $entity, EntityManager $manager) {
                $entity->setIsEnabled(true);

                $manager->update($entity);
            })
            ->setBatchAction('index', 'disable', 'Disable', function (EntityInterface $entity, EntityManager $manager) {
                $entity->setIsEnabled(false);

                $manager->update($entity);
            })
            ->setBatchAction('index', 'delete', 'Delete', function (EntityInterface $entity, EntityManager $manager) {
                $manager->delete($entity);
            })
        ;
    }

    protected function getSection(): string
    {
        return 'site_navigation';
    }
}
