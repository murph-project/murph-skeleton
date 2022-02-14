<?php

namespace App\Core\EventListener;

use App\Core\Repository\RedirectRepositoryQuery;
use App\Core\Router\RedirectMatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * class RedirectListener.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class RedirectListener
{
    protected RedirectMatcher $matcher;
    protected RedirectRepositoryQuery $repository;

    public function __construct(RedirectMatcher $matcher, RedirectRepositoryQuery $repository)
    {
        $this->matcher = $matcher;
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

        $uri = $event->getRequest()->getUri();

        foreach ($redirects as $redirect) {
            if ($this->matcher->match($redirect, $uri)) {
                if ($redirect->getReuseQueryString() && count($event->getRequest()->query)) {
                    $query = sprintf('?%s', http_build_query($event->getRequest()->query->all()));
                } else {
                    $query = '';
                }

                $event->setResponse(new RedirectResponse(
                    $redirect->getLocation().$query,
                    $redirect->getRedirectCode()
                ));
            }
        }
    }
}
