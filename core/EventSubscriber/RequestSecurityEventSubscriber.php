<?php

namespace App\Core\EventSubscriber;

use App\Core\Repository\Site\NodeRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * class RequestSecurityEventSubscriber.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class RequestSecurityEventSubscriber implements EventSubscriberInterface
{
    protected NodeRepository $nodeRepository;
    protected AuthorizationChecker $authorizationChecker;

    public function __construct(NodeRepository $nodeRepository, ContainerInterface $container)
    {
        $this->nodeRepository = $nodeRepository;
        $this->authorizationChecker = $container->get('security.authorization_checker');
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->has('_node')) {
            return;
        }

        $node = $this->nodeRepository->findOneBy([
            'id' => $request->attributes->get('_node'),
        ]);

        $roles = $node->getSecurityRoles();

        if (empty($roles)) {
            return;
        }

        $operator = $node->getSecurityOperator();
        $exception = new AccessDeniedException('Access denied.');

        foreach ($roles as $role) {
            $isGranted = $this->authorizationChecker->isGranted($role);

            if ('or' === $operator && $isGranted) {
                return;
            }
            if ('and' === $operator && !$isGranted) {
                throw $exception;
            }
        }

        throw $exception;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['onKernelRequest', 1],
        ];
    }
}
