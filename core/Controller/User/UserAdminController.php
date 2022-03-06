<?php

namespace App\Core\Controller\User;

use App\Core\Controller\Admin\Crud\CrudController;
use App\Core\Crud\CrudConfiguration;
use App\Core\Crud\Field;
use App\Core\Event\Account\PasswordRequestEvent;
use App\Core\Factory\UserFactory as Factory;
use App\Core\Manager\EntityManager;
use App\Entity\User as Entity;
use App\Form\UserType as Type;
use App\Repository\UserRepositoryQuery as RepositoryQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use App\Core\Security\TokenGenerator;

class UserAdminController extends CrudController
{
    /**
     * @Route("/admin/user/{page}", name="admin_user_index", methods={"GET"}, requirements={"page":"\d+"})
     */
    public function index(RepositoryQuery $query, Request $request, Session $session, int $page = 1): Response
    {
        return $this->doIndex($page, $query, $request, $session);
    }

    /**
     * @Route("/admin/user/new", name="admin_user_new", methods={"GET", "POST"})
     */
    public function new(Factory $factory, EntityManager $entityManager, Request $request, TokenGenerator $tokenGenerator): Response
    {
        return $this->doNew($factory->create(null, $tokenGenerator->generateToken()), $entityManager, $request);
    }

    /**
     * @Route("/admin/user/show/{entity}", name="admin_user_show", methods={"GET"})
     */
    public function show(Entity $entity): Response
    {
        return $this->doShow($entity);
    }

    /**
     * @Route("/admin/user/filter", name="admin_user_filter", methods={"GET"})
     */
    public function filter(Session $session): Response
    {
        return $this->doFilter($session);
    }

    /**
     * @Route("/admin/user/edit/{entity}", name="admin_user_edit", methods={"GET", "POST"})
     */
    public function edit(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        return $this->doEdit($entity, $entityManager, $request);
    }

    /**
     * @Route("/admin/user/delete/{entity}", name="admin_user_delete", methods={"DELETE"})
     */
    public function delete(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        return $this->doDelete($entity, $entityManager, $request);
    }

    /**
     * @Route("/admin/user/resetting_request/{entity}", name="admin_user_resetting_request", methods={"POST"})
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

    protected function getConfiguration(): CrudConfiguration
    {
        return CrudConfiguration::create()
            ->setPageTitle('index', 'Users')
            ->setPageTitle('edit', '{username}')
            ->setPageTitle('new', 'New user')
            ->setPageTitle('show', '{username}')

            ->setPageRoute('index', 'admin_user_index')
            ->setPageRoute('new', 'admin_user_new')
            ->setPageRoute('edit', 'admin_user_edit')
            ->setPageRoute('show', 'admin_user_show')
            ->setPageRoute('delete', 'admin_user_delete')
            ->setPageRoute('filter', 'admin_user_filter')

            ->setForm('edit', Type::class, [])
            ->setForm('new', Type::class)

            ->setView('form', '@Core/user/user_admin/_form.html.twig')
            ->setView('index', '@Core/user/user_admin/index.html.twig')
            ->setView('new', '@Core/user/user_admin/new.html.twig')
            ->setView('show', '@Core/user/user_admin/show.html.twig')
            ->setView('show_entity', '@Core/user/user_admin/_show.html.twig')
            ->setView('edit', '@Core/user/user_admin/edit.html.twig')

            ->setDefaultSort('index', 'username')

            ->setField('index', 'E-mail', Field\TextField::class, [
                'property' => 'email',
                'sort' => ['email', '.email'],
                'attr' => ['class' => 'miw-200'],
            ])
            ->setField('index', 'Display name', Field\TextField::class, [
                'property' => 'displayName',
                'sort' => ['displayName', '.displayName'],
                'attr' => ['class' => 'miw-200'],
            ])
        ;
    }

    protected function getSection(): string
    {
        return 'user';
    }
}
