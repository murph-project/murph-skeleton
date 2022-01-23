<?php

namespace App\Core\Controller\Setting;

use App\Core\Controller\Admin\AdminController;
use App\Core\Entity\Setting as Entity;
use App\Core\Event\Setting\SettingEvent;
use App\Core\Manager\EntityManager;
use App\Core\Repository\SettingRepositoryQuery as RepositoryQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/setting")
 */
class SettingAdminController extends AdminController
{
    /**
     * @Route("/{page}", name="admin_setting_index", requirements={"page": "\d+"})
     */
    public function index(
        RepositoryQuery $query,
        EventDispatcherInterface $eventDispatcher,
        Request $request,
        int $page = 1
    ): Response {
        $eventDispatcher->dispatch(new SettingEvent(), SettingEvent::INIT_EVENT);

        $pager = $query
            ->orderBy('.section, .label')
            ->paginate($page)
        ;

        return $this->render('@Core/setting/setting_admin/index.html.twig', [
            'pager' => $pager,
        ]);
    }

    /**
     * @Route("/edit/{entity}", name="admin_setting_edit")
     */
    public function edit(
        Entity $entity,
        EntityManager $entityManager,
        EventDispatcherInterface $eventDispatcher,
        Request $request
    ): Response {
        $builder = $this->createFormBuilder($entity);
        $event = new SettingEvent([
            'builder' => $builder,
            'entity' => $entity,
            'options' => [],
        ]);

        $eventDispatcher->dispatch($event, SettingEvent::FORM_INIT_EVENT);

        $form = $builder->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager->update($entity);
                $this->addFlash('success', 'The data has been saved.');

                return $this->redirectToRoute('admin_setting_index');
            }

            $this->addFlash('warning', 'The form is not valid.');
        }

        return $this->render('@Core/setting/setting_admin/edit.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
            'options' => $event->getData()['options'],
        ]);
    }

    /**
     * @Route("/delete/{entity}", name="admin_setting_delete", methods={"DELETE"})
     */
    public function delete(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->request->get('_token'))) {
            $entityManager->delete($entity);

            $this->addFlash('success', 'The data has been removed.');
        }

        return $this->redirectToRoute('admin_setting_index');
    }

    public function getSection(): string
    {
        return 'setting';
    }
}
