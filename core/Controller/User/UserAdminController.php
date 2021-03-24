<?php

namespace App\Core\Controller\User;

use App\Core\Controller\Admin\AdminController;
use App\Core\Event\Account\PasswordRequestEvent;
use App\Core\Factory\UserFactory as EntityFactory;
use App\Core\Form\UserType as EntityType;
use App\Core\Manager\EntityManager;
use App\Entity\User as Entity;
use App\Repository\UserRepositoryQuery as RepositoryQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/user")
 */
class UserAdminController extends AdminController
{
    /**
     * @Route("/{page}", name="admin_user_index", requirements={"page": "\d+"})
     */
    public function index(int $page = 1, RepositoryQuery $query, Request $request): Response
    {
        $pager = $query->paginate($page);

        return $this->render('@Core/user/user_admin/index.html.twig', [
            'pager' => $pager,
        ]);
    }

    /**
     * @Route("/new", name="admin_user_new")
     */
    public function new(
        EntityFactory $factory,
        EntityManager $entityManager,
        UserPasswordEncoderInterface $encoder,
        Request $request
    ): Response {
        $entity = $factory->create($this->getUser());
        $form = $this->createForm(EntityType::class, $entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager->create($entity);
                $this->addFlash('success', 'The data has been saved.');

                return $this->redirectToRoute('admin_user_edit', [
                    'entity' => $entity->getId(),
                ]);
            }
            $this->addFlash('warning', 'The form is not valid.');
        }

        return $this->render('@Core/user/user_admin/new.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/edit/{entity}", name="admin_user_edit")
     */
    public function edit(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        $form = $this->createForm(EntityType::class, $entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $entityManager->update($entity);
                $this->addFlash('success', 'The data has been saved.');

                return $this->redirectToRoute('admin_user_edit', [
                    'entity' => $entity->getId(),
                ]);
            }
            $this->addFlash('warning', 'The form is not valid.');
        }

        return $this->render('@Core/user/user_admin/edit.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/show/{entity}", name="admin_user_show")
     */
    public function show(Entity $entity): Response
    {
        return $this->render('@Core/user/user_admin/show.html.twig', [
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/resetting_request/{entity}", name="admin_user_resetting_request", methods={"POST"})
     */
    public function requestResetting(Entity $entity, EventDispatcherInterface $eventDispatcher, Request $request): Response
    {
        if ($this->isCsrfTokenValid('resetting_request'.$entity->getId(), $request->request->get('_token'))) {
            $eventDispatcher->dispatch(new PasswordRequestEvent($entity), PasswordRequestEvent::EVENT);

            $this->addFlash('success', 'E-mail sent.');
        }

        return $this->redirectToRoute('admin_user_edit', [
            'entity' => $entity->getId(),
        ]);
    }

    /**
     * @Route("/delete/{entity}", name="admin_user_delete", methods={"DELETE"})
     */
    public function delete(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->request->get('_token'))) {
            $entityManager->delete($entity);

            $this->addFlash('success', 'The data has been removed.');
        }

        return $this->redirectToRoute('admin_user_index');
    }

    public function getSection(): string
    {
        return 'user';
    }
}
