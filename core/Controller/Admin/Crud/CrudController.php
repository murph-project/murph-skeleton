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

    abstract protected function getConfiguration(): CrudConfiguration;

    protected function doIndex(int $page = 1, RepositoryQuery $query, Request $request, Session $session): Response
    {
        $configuration = $this->getConfiguration();

        $this->updateFilters($request, $session);

        $pager = $query
            ->useFilters($this->filters)
            ->paginate($page, $configuration->getMaxPerPage('index'))
        ;

        return $this->render($this->getConfiguration()->getView('index'), [
            'configuration' => $configuration,
            'pager' => $pager,
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

        $form = $this->createForm($configuration->getForm('new'), $entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($beforeCreate !== null) {
                    call_user_func_array($beforeCreate, [$entity, $form, $request]);
                }

                $entityManager->create($entity);
                $this->addFlash('success', 'The data has been saved.');

                return $this->redirectToRoute($configuration->getPageRoute('edit'), [
                    'entity' => $entity->getId(),
                ]);
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

        $form = $this->createForm($configuration->getForm('edit'), $entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($beforeUpdate !== null) {
                    call_user_func_array($beforeUpdate, [$entity, $form, $request]);
                }

                $entityManager->update($entity);
                $this->addFlash('success', 'The data has been saved.');

                return $this->redirectToRoute($configuration->getPageRoute('edit'), [
                    'entity' => $entity->getId(),
                ]);
            }
            $this->addFlash('warning', 'The form is not valid.');
        }

        return $this->render($configuration->getView('edit'), [
            'form' => $form->createView(),
            'configuration' => $configuration,
            'entity' => $entity,
        ]);
    }

    protected function doDelete(EntityInterface $entity, EntityManager $entityManager, Request $request, callable $beforeDelete = null): Response
    {
        $configuration = $this->getConfiguration();

        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->request->get('_token'))) {
            if ($beforeDelete !== null) {
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
}
