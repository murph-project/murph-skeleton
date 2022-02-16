<?php

namespace App\Core\Router;

use App\Core\Entity\Redirect;
use App\Core\String\UrlBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * class RedirectBuilder.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class RedirectBuilder
{
    protected UrlBuilder $urlBuilder;

    public function __construct(UrlBuilder $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    public function buildResponse(Redirect $redirect, Request $request): RedirectResponse
    {
        return new RedirectResponse(
            $this->buildUrl($redirect, $request),
            $redirect->getRedirectCode()
        );
    }

    public function buildUrl(Redirect $redirect, Request $request): string
    {
        $data = $this->parse($request->getUri());

        if ('path' === $redirect->getRuleType()) {
            $location = $redirect->getLocation();
        } else {
            $location = preg_replace('`'.$redirect->getRule().'`sU', $redirect->getLocation(), $data['path']);
        }

        $location = $this->urlBuilder->replaceTags($location);

        if ($redirect->getReuseQueryString() && count($request->query)) {
            $location .= sprintf('?%s', http_build_query($request->query->all()));
        }

        return $location;
    }

    protected function parse($url): array
    {
        return parse_url($url);
    }
}
