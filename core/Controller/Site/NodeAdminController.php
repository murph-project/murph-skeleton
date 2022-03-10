<?php

namespace App\Core\Controller\Site;

use App\Core\Controller\Admin\AdminController;
use App\Core\Entity\Site\Node;
use App\Core\Entity\Site\Node as Entity;
use App\Core\Entity\Site\Page\Page;
use App\Core\Event\EntityManager\EntityManagerEvent;
use App\Core\Factory\Site\NodeFactory as EntityFactory;
use App\Core\Factory\Site\Page\PageFactory;
use App\Core\Form\Site\NodeMoveType;
use App\Core\Form\Site\NodeType as EntityType;
use App\Core\Manager\EntityManager;
use App\Core\Repository\Site\NodeRepository;
use App\Core\Site\ControllerLocator;
use App\Core\Site\RoleLocator;
use App\Core\Site\PageLocator;
use App\Core\Sitemap\SitemapBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/site/node")
 */
class NodeAdminController extends AbstractController
{
    /**
     * @Route("/new/{node}", name="admin_site_node_new")
     */
    public function new(
        Node $node,
        EntityFactory $factory,
        PageFactory $pageFactory,
        EntityManager $entityManager,
        NodeRepository $nodeRepository,
        PageLocator $pageLocator,
        ControllerLocator $controllerLocator,
        RoleLocator $roleLocator,
        Request $request
    ): Response {
        $entity = $factory->create($node->getMenu());
        $form = $this->createForm(EntityType::class, $entity, [
            'pages' => $pageLocator->getPages(),
            'controllers' => $controllerLocator->getControllers(),
            'roles' => $roleLocator->getRoles(),
            'navigation' => $node->getMenu()->getNavigation(),
        ]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $position = $form->get('position')->getData();

                $parent = 'above' === $position ? $node : $node->getParent();
                $entity->setParent($parent);

                if ('above' === $position) {
                    $nodeRepository->persistAsLastChild($entity, $node);
                } else {
                    if ('after' === $position) {
                        $nodeRepository->persistAsNextSiblingOf($entity, $node);
                    } elseif ('before' === $position) {
                        $nodeRepository->persistAsPrevSiblingOf($entity, $node);
                    }
                }

                $this->handlePageAssociation(
                    $form->get('pageAction')->getData(),
                    $form->get('pageEntity')->getData(),
                    $form->get('pageType')->getData(),
                    $entity,
                    $pageFactory,
                    $pageLocator
                );

                $entityManager->create($entity);

                $this->addFlash('success', 'The data has been saved.');

                return $this->redirect($this->generateUrl('admin_site_tree_navigation', [
                    'navigation' => $node->getMenu()->getNavigation()->getId(),
                    'data-modal' => $this->generateUrl('admin_site_node_edit', ['entity' => $entity->getId()]),
                ]).sprintf('#node-%d', $entity->getId()));
            }
            $this->addFlash('warning', 'The form is not valid.');

            return $this->redirect($this->generateUrl('admin_site_tree_navigation', [
                'navigation' => $entity->getMenu()->getNavigation()->getId(),
            ]).sprintf('#node-%d', $entity->getId()));
        }

        return $this->render('@Core/site/node_admin/new.html.twig', [
            'form' => $form->createView(),
            'node' => $node,
            'entity' => $entity,
            'tab' => 'content',
        ]);
    }

    /**
     * @Route("/edit/{entity}/{tab}", name="admin_site_node_edit")
     */
    public function edit(
        Entity $entity,
        EntityManager $entityManager,
        PageFactory $pageFactory,
        PageLocator $pageLocator,
        ControllerLocator $controllerLocator,
        RoleLocator $roleLocator,
        Request $request,
        string $tab = 'content'
    ): Response {
        $form = $this->createForm(EntityType::class, $entity, [
            'pages' => $pageLocator->getPages(),
            'controllers' => $controllerLocator->getControllers(),
            'roles' => $roleLocator->getRoles(),
            'navigation' => $entity->getMenu()->getNavigation(),
        ]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->handlePageAssociation(
                    $form->get('pageAction')->getData(),
                    $form->get('pageEntity')->getData(),
                    $form->get('pageType')->getData(),
                    $entity,
                    $pageFactory,
                    $pageLocator
                );

                $entityManager->update($entity);

                $this->addFlash('success', 'The data has been saved.');
            } else {
                $this->addFlash('warning', 'The form is not valid.');
            }

            return $this->redirect($this->generateUrl('admin_site_tree_navigation', [
                'navigation' => $entity->getMenu()->getNavigation()->getId(),
                'data-modal' => $this->generateUrl('admin_site_node_edit', ['entity' => $entity->getId()]),
            ]).sprintf('#node-%d', $entity->getId()));
        }

        $page = $entity->getPage();

        if ($page !== null) {
            $pageConfiguration = $pageLocator->getPages()[get_class($page)] ?? null;
        } else {
            $pageConfiguration = null;
        }

        return $this->render('@Core/site/node_admin/edit.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
            'page' => $page,
            'pageConfiguration' => $pageConfiguration,
            'tab' => $tab,
        ]);
    }

    /**
     * @Route("/urls/{entity}", name="admin_site_node_urls")
     */
    public function urls(Entity $entity, SitemapBuilder $builder): Response
    {
        return $this->render('@Core/site/node_admin/urls.html.twig', [
            'entity' => $entity,
            'urls' => $builder->getNodeUrls($entity),
        ]);
    }

    /**
     * @Route("/move/{entity}", name="admin_site_node_move")
     */
    public function move(
        Entity $entity,
        EntityManager $entityManager,
        NodeRepository $nodeRepository,
        Request $request
    ): Response {
        $form = $this->createForm(NodeMoveType::class, null, [
            'menu' => $entity->getMenu(),
        ]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->get('node')->getData()->getId() === $entity->getId()) {
                $form->get('node')->addError(new FormError('Élement de référence invalide.'));
            }

            if ($form->isValid()) {
                $position = $form->get('position')->getData();
                $node = $form->get('node')->getData();

                $parent = 'above' === $position ? $node : $node->getParent();
                $entity->setParent($parent);

                if ('above' === $position) {
                    $nodeRepository->persistAsLastChild($entity, $node);
                    $entityManager->flush();
                } else {
                    if ('after' === $position) {
                        $nodeRepository->persistAsNextSiblingOf($entity, $node);
                    } elseif ('before' === $position) {
                        $nodeRepository->persistAsPrevSiblingOf($entity, $node);
                    }

                    $entityManager->flush();
                }

                $this->addFlash('success', 'The data has been saved.');
            } else {
                $this->addFlash('warning', 'The form is not valid.');
            }

            return $this->redirect($this->generateUrl('admin_site_tree_navigation', [
                'navigation' => $entity->getMenu()->getNavigation()->getId(),
            ]).sprintf('#node-%d', $entity->getId()));
        }

        return $this->render('@Core/site/node_admin/move.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/toggle/visibility/{entity}", name="admin_site_node_toggle_visibility", methods={"POST"})
     */
    public function toggleVisibility(Entity $entity, EntityManager $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('toggle_visibility'.$entity->getId(), $request->request->get('_token'))) {
            $entity->setIsVisible(!$entity->getIsVisible());

            $entityManager->update($entity);

            $this->addFlash('success', 'The data has been saved.');
        }

        return $this->redirect($this->generateUrl('admin_site_tree_navigation', [
            'navigation' => $entity->getMenu()->getNavigation()->getId(),
        ]).sprintf('#node-%d', $entity->getId()));
    }

    /**
     * @Route("/delete/{entity}", name="admin_site_node_delete", methods={"DELETE"})
     */
    public function delete(
        Entity $entity,
        NodeRepository $nodeRepository,
        EventDispatcherInterface $eventDispatcher,
        Request $request
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->request->get('_token'))) {
            $eventDispatcher->dispatch(new EntityManagerEvent($entity), EntityManagerEvent::PRE_DELETE_EVENT);
            $nodeRepository->removeFromTree($entity);
            $nodeRepository->reorder($entity->getMenu()->getRootNode());
            $eventDispatcher->dispatch(new EntityManagerEvent($entity), EntityManagerEvent::DELETE_EVENT);

            $this->addFlash('success', 'Donnée supprimée.');
        }

        return $this->redirectToRoute('admin_site_tree_navigation', [
            'navigation' => $entity->getMenu()->getNavigation()->getId(),
        ]);
    }

    protected function handlePageAssociation(
        string $pageAction,
        ?Page $pageEntity,
        string $pageType,
        Entity $entity,
        PageFactory $pageFactory,
        PageLocator $pageLocator
    ) {
        if ('new' === $pageAction) {
            $pageConfiguration = $pageLocator->getPage($pageType);
            $page = $pageFactory->create($pageType, $entity->getLabel());
            $page->setTemplate($pageConfiguration->getTemplates()[0]['file']);

            $entity
                ->setPage($page)
                ->setAliasNode(null)
            ;
        } elseif ('existing' === $pageAction) {
            if ($pageEntity) {
                $entity->setPage($pageEntity);
            } else {
                $this->addFlash('info', 'Aucun changement de page effectué.');
            }
            $entity->setAliasNode(null);
        } elseif ('alias' === $pageAction) {
            $entity->setPage(null);
        } elseif ('none' === $pageAction) {
            $entity
                ->setPage(null)
                ->setAliasNode(null)
            ;
        }
    }
}
