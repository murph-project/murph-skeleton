<?php

namespace App\Core\Controller\Admin\Crud;

use App\Core\Controller\Admin\AdminController;
use App\Core\Crud\CrudConfiguration;
use App\Core\Entity\EntityInterface;
use App\Core\Manager\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Core\Repository\RepositoryQuery;

/**
 * class CrudController.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
abstract class CrudController extends AdminController
{
    abstract protected function getConfiguration(): CrudConfiguration;

    protected function doIndex(int $page = 1, RepositoryQuery $query, Request $request, Session $session): Response
    {
        /*$this->updateFilters($options['request'], $options['session']);*/

        $pager = $query
            //->useFilters($this->filters)
            ->paginate($page)
        ;

        /*$viewOptions = array_merge([
            'pager' => $pager,
            'hasFilters' => !empty($this->filters),
        ], $options['viewOptions']);*/

        $configuration = $this->getConfiguration();

        return $this->render($this->getConfiguration()->getView('index'), [
            'configuration' => $configuration,
            'pager' => $pager,
        ]);
    }

    protected function doNew(EntityInterface $entity, EntityManager $entityManager, Request $request): Response
    {
        $form = $this->createForm($this->forms['new'], $entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
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

    protected function doEdit(EntityInterface $entity, EntityManager $entityManager, Request $request): Response
    {
        $configuration = $this->getConfiguration();

        $form = $this->createForm($configuration->getForm('edit'), $entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
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

    protected function doDelete(EntityInterface $entity, EntityManager $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->request->get('_token'))) {
            $entityManager->delete($entity);

            $this->addFlash('success', 'The data has been removed.');
        }

        return $this->redirectToRoute($configuration->getPageRoute('index'));
    }

    protected function doFilter(Session $session): Response
    {
        $form = $this->createForm($this->forms['filters']);
        $form->submit($session->get($this->filterRequestId, []));

        return $this->render($this->getView('filters'), [
            'form' => $form->createView(),
        ]);
    }

    protected function updateFilters(Request $request, Session $session)
    {
        if ($request->query->has($this->filterRequestId)) {
            $filters = $request->query->get($this->filterRequestId);

            if ('0' === $filters) {
                $filters = [];
            }
        } elseif ($session->has($this->filterRequestId)) {
            $filters = $session->get($this->filterRequestId);
        } else {
            $filters = [];
        }

        if (isset($this->forms['filters'])) {
            $form = $this->createForm($this->forms['filters']);
            $form->submit($filters);
        } else {
            $form = null;
        }

        if (empty($filters)) {
            $this->filters = $filters;
            $session->set($this->filterRequestId, $filters);
        } elseif (null !== $form && $form->isValid()) {
            $this->filters = $form->getData();
            $session->set($this->filterRequestId, $filters);
        }
    }
}
