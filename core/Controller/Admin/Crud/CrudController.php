<?php

namespace App\Core\Controller\Admin\Crud;

use App\Core\Controller\Admin\AdminController;
use App\Core\Crud\CrudConfiguration;
use App\Core\Entity\EntityInterface;
use App\Core\Manager\EntityManager;
use App\Core\Repository\RepositoryQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * class CrudController.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
abstract class CrudController extends AdminController
{
    protected array $filters = [];
    protected array $sort = [
        'label' => null,
        'direction' => null,
    ];

    abstract protected function getConfiguration(): CrudConfiguration;

    protected function doIndex(int $page = 1, RepositoryQuery $query, Request $request, Session $session): Response
    {
        $configuration = $this->getConfiguration();

        $this->applySort('index', $query, $request);
        $this->updateFilters($request, $session);

        $pager = $query
            ->usefilters($this->filters)
            ->paginate($page, $configuration->getmaxperpage('index'))
        ;

        return $this->render($this->getConfiguration()->getView('index'), [
            'configuration' => $configuration,
            'pager' => $pager,
            'sort' => $this->sort,
            'filters' => [
                'show' => null !== $configuration->getForm('filter'),
                'isEmpty' => empty($this->filters),
            ],
        ]);
    }

    protected function doNew(EntityInterface $entity, EntityManager $entityManager, Request $request, callable $beforeCreate = null): Response
    {
        $configuration = $this->getConfiguration();

        $this->prepareEntity($entity);

        $form = $this->createForm($configuration->getForm('new'), $entity, $configuration->getFormOptions('new'));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if (null !== $beforeCreate) {
                    call_user_func_array($beforeCreate, [$entity, $form, $request]);
                }

                $entityManager->create($entity);
                $this->addFlash('success', 'The data has been saved.');

                return $this->redirectToRoute($configuration->getPageRoute('edit'), array_merge(
                    ['entity' => $entity->getId()],
                    $configuration->getPageRouteParams('edit')
                ));
            }
            $this->addFlash('warning', 'The form is not valid.');
        }

        return $this->render($configuration->getView('new'), [
            'form' => $form->createView(),
            'configuration' => $configuration,
            'entity' => $entity,
        ]);
    }

    protected function doShow(EntityInterface $entity): Response
    {
        $configuration = $this->getConfiguration();

        return $this->render($configuration->getView('show'), [
            'entity' => $entity,
            'configuration' => $configuration,
        ]);
    }

    protected function doEdit(EntityInterface $entity, EntityManager $entityManager, Request $request, callable $beforeUpdate = null): Response
    {
        $configuration = $this->getConfiguration();

        $this->prepareEntity($entity);

        $form = $this->createForm($configuration->getForm('edit'), $entity, $configuration->getFormOptions('edit'));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if (null !== $beforeUpdate) {
                    call_user_func_array($beforeUpdate, [$entity, $form, $request]);
                }

                $entityManager->update($entity);
                $this->addFlash('success', 'The data has been saved.');

                return $this->redirectToRoute($configuration->getPageRoute('edit'), array_merge(
                    ['entity' => $entity->getId()],
                    $configuration->getPageRouteParams('edit')
                ));
            }
            $this->addFlash('warning', 'The form is not valid.');
        }

        return $this->render($configuration->getView('edit'), [
            'form' => $form->createView(),
            'configuration' => $configuration,
            'entity' => $entity,
        ]);
    }

    protected function doSort(int $page = 1, RepositoryQuery $query, EntityManager $entityManager, Request $request, Session $session): Response
    {
        $configuration = $this->getConfiguration();
        $context = $request->query->get('context', 'index');

        if (!$configuration->getIsSortableCollection($context)) {
            throw $this->createNotFoundException();
        }

        $this->applySort($context, $query, $request);
        $this->updateFilters($request, $session);

        $pager = $query
            ->useFilters($this->filters)
            ->paginate($page, $configuration->getMaxPerPage($context))
        ;

        if ($this->isCsrfTokenValid('sort', $request->query->get('_token'))) {
            $items = $request->request->get('items', []);
            $setter = 'set'.$configuration->getSortableCollectionProperty();
            $orderStart = ($page - 1) * $configuration->getMaxPerPage($context);

            foreach ($pager as $key => $entity) {
                if (isset($items[$key + 1])) {
                    $entity->{$setter}($items[$key + 1] + $orderStart);

                    $entityManager->update($entity);
                }
            }

            $this->addFlash('success', 'The data has been saved.');
        } else {
            $this->addFlash('warning', 'The form is not valid.');
        }

        return $this->json([]);
    }

    protected function doBatch(int $page = 1, RepositoryQuery $query, EntityManager $entityManager, Request $request, Session $session): Response
    {
        $configuration = $this->getConfiguration();
        $datas = $request->request->get('batch', []);

        $context = $datas['context'] ?? 'index';
        $target = $datas['target'] ?? null;
        $action = $datas['action'] ?? null;
        $token = $datas['_token'] ?? null;
        $items = $datas['items'] ?? [];
        $batchAction = $configuration->getBatchAction($context, $action);

        if (empty($context) || empty($action) || empty($target)) {
            return $this->json([]);
        }

        if (!$this->isCsrfTokenValid('batch', $token) || empty($batchAction)) {
            $this->addFlash('warning', 'The form is not valid.');

            return $this->json([]);
        }

        $callback = $batchAction['callback'];

        $this->applySort($context, $query, $request);
        $this->updateFilters($request, $session);

        $query->useFilters($this->filters);

        if ('selection' === $target) {
            $isSelection = true;
            $pager = $query->paginate($page, $configuration->getMaxPerPage($context));
        } else {
            $isSelection = false;
            $pager = $query->find();
        }

        foreach ($pager as $key => $entity) {
            if (($isSelection && isset($items[$key + 1])) || !$isSelection) {
                $callback($entity, $entityManager);
            }
        }

        $this->addFlash('success', 'Batch action done.');

        return $this->json([]);
    }

    protected function doDelete(EntityInterface $entity, EntityManager $entityManager, Request $request, callable $beforeDelete = null): Response
    {
        $configuration = $this->getConfiguration();

        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->request->get('_token'))) {
            if (null !== $beforeDelete) {
                call_user_func($beforeDelete, $entity);
            }

            $entityManager->delete($entity);

            $this->addFlash('success', 'The data has been removed.');
        }

        return $this->redirectToRoute($configuration->getPageRoute('index'));
    }

    protected function doFilter(Session $session): Response
    {
        $configuration = $this->getConfiguration();
        $type = $configuration->getForm('filter');

        if (null === $type) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm($type);
        $form->submit($session->get($form->getName(), []));

        return $this->render($configuration->getView('filter'), [
            'form' => $form->createView(),
            'configuration' => $configuration,
        ]);
    }

    protected function updateFilters(Request $request, Session $session)
    {
        $configuration = $this->getConfiguration();
        $type = $configuration->getForm('filter');

        if (null === $type) {
            return;
        }

        $form = $this->createForm($type);

        if ($request->query->has($form->getName())) {
            $filters = $request->query->get($form->getName());

            if ('0' === $filters) {
                $filters = [];
            }
        } elseif ($session->has($form->getName())) {
            $filters = $session->get($form->getName());
        } else {
            $filters = [];
        }

        $form->submit($filters);

        if (empty($filters)) {
            $this->filters = $filters;
            $session->set($form->getName(), $filters);
        } elseif ($form->isValid()) {
            $this->filters = $form->getData();
            $session->set($form->getName(), $filters);
        }
    }

    protected function prepareEntity(EntityInterface $entity)
    {
        $configuration = $this->getConfiguration();

        if ($configuration->isI18n()) {
            foreach ($configuration->getLocales() as $locale) {
                $entity->addTranslation($entity->translate($locale, false));
            }
        }
    }

    protected function applySort(string $context, RepositoryQuery $query, Request $request)
    {
        $configuration = $this->getConfiguration();

        if ($configuration->getIsSortableCollection($context)) {
            $query->orderBy(sprintf('.%s', $configuration->getSortableCollectionProperty()));

            return;
        }

        $defaultSort = $configuration->getDefaultSort($context);

        $name = $request->query->get('_sort', $defaultSort['label'] ?? null);
        $direction = strtolower($request->query->get('_sort_direction', $defaultSort['direction'] ?? 'asc'));

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        foreach ($configuration->getFields($context) as $label => $field) {
            $sortOption = $field['options']['sort'] ?? null;

            if (null === $sortOption) {
                continue;
            }

            if ($sortOption[0] !== $name) {
                continue;
            }

            $sorter = $sortOption[1];

            if (is_string($sorter)) {
                $query->orderBy($sorter, $direction);
            } else {
                call_user_func_array($sorter, [$query, $direction]);
            }

            $this->sort = [
                'label' => $label,
                'direction' => $direction,
            ];

            return;
        }
    }
}
