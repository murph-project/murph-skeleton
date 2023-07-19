<?php

namespace App\Controller;

use App\Core\Controller\User\UserAdminController as BaseUserAdminController;
use App\Core\Crud\CrudConfiguration;
use App\Core\Factory\UserFactory as Factory;
use App\Core\Manager\EntityManager;
use App\Core\Security\TokenGenerator;
use App\Entity\User as Entity;
use App\Repository\UserRepositoryQuery as RepositoryQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class UserAdminController extends BaseUserAdminController
{
    #[Route(path: '/admin/user/{page}', name: 'admin_user_index', methods: ['GET'], requirements: ['page' => '\d+'])]
    public function index(RepositoryQuery $query, Request $request, Session $session, int $page = 1): Response
    {
        return parent::index($query, $request, $session, $page);
    }

    #[Route(path: '/admin/user/new', name: 'admin_user_new', methods: ['GET', 'POST'])]
    public function new(Factory $factory, EntityManager $entityManager, Request $request, TokenGenerator $tokenGenerator): Response
    {
        return parent::new($factory, $entityManager, $request, $tokenGenerator);
    }

    #[Route(path: '/admin/user/show/{entity}', name: 'admin_user_show', methods: ['GET'])]
    public function show(Entity $entity): Response
    {
        return parent::show($entity);
    }

    #[Route(path: '/admin/user/filter', name: 'admin_user_filter', methods: ['GET'])]
    public function filter(Session $session): Response
    {
        return parent::filter($session);
    }

    #[Route(path: '/admin/user/edit/{entity}', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        return parent::edit($entity, $entityManager, $request);
    }

    #[Route(path: '/admin/user/inline_edit/{entity}/{context}/{label}', name: 'admin_user_inline_edit', methods: ['GET', 'POST'])]
    public function inlineEdit(string $context, string $label, Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        return parent::inlineEdit($context, $label, $entity, $entityManager, $request);
    }

    #[Route(path: '/admin/user/delete/{entity}', name: 'admin_user_delete', methods: ['DELETE', 'POST'])]
    public function delete(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        return parent::delete($entity, $entityManager, $request);
    }

    #[Route(path: '/admin/user/resetting_request/{entity}', name: 'admin_user_resetting_request', methods: ['POST'])]
    public function requestResetting(Entity $entity, EventDispatcherInterface $eventDispatcher, Request $request): Response
    {
        return parent::requestResetting($entity, $eventDispatcher, $request);
    }

    protected function getConfiguration(): CrudConfiguration
    {
        if ($this->configuration) {
            return $this->configuration;
        }

        return parent::getConfiguration()
            ->setView('form', 'admin/user_admin/_form.html.twig')
            ->setView('show_entity', 'admin/user_admin/_show.html.twig')
        ;
    }
}
