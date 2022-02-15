<?php

namespace App\Core\EventListener;

use App\Core\Repository\RedirectRepositoryQuery;
use App\Core\Router\RedirectMatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Core\Router\RedirectBuilder;

/**
 * class RedirectListener.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class RedirectListener
{
    protected RedirectMatcher $matcher;
    protected RedirectBuilder $builder;
    protected RedirectRepositoryQuery $repository;

    public function __construct(RedirectMatcher $matcher, RedirectBuilder $builder, RedirectRepositoryQuery $repository)
    {
        $this->matcher = $matcher;
        $this->builder = $builder;
        $this->repository = $repository;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $request = $event->getRequest();

        if (!$event->getThrowable() instanceof NotFoundHttpException) {
            return;
        }

        $redirects = $this->repository
            ->orderBy('.sortOrder')
            ->where('.isEnabled=1')
            ->find()
        ;

        foreach ($redirects as $redirect) {
            if ($this->matcher->match($redirect, $event->getRequest()->getUri())) {
                $event->setResponse($this->builder->buildResponse($redirect, $event->getRequest()));
            }
        }
    }
}
