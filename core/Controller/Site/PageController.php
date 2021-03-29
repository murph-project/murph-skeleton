<?php

namespace App\Core\Controller\Site;

use App\Core\Site\SiteRequest;
use App\Core\Site\SiteStore;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController extends AbstractController
{
    protected SiteRequest $siteRequest;
    protected SiteStore $siteStore;

    public function __construct(SiteRequest $siteRequest, SiteStore $siteStore)
    {
        $this->siteRequest = $siteRequest;
        $this->siteStore = $siteStore;
    }

    public function show(): Response
    {
        if (!$this->siteRequest->getPage()) {
            throw $this->createNotFoundException();
        }

        return $this->defaultRender($this->siteRequest->getPage()->getTemplate());
    }

    protected function defaultRender(string $view, array $parameters = [], Response $response = null): Response
    {
        $parameters = array_merge($this->getDefaultRenderParameters(), $parameters);

        return parent::render($view, $parameters, $response);
    }

    protected function getDefaultRenderParameters(): array
    {
        return [
            '_node' => $this->siteRequest->getNode(),
            '_page' => $this->siteRequest->getPage(),
            '_menu' => $this->siteRequest->getMenu(),
            '_navigation' => $this->siteRequest->getNavigation(),
            '_store' => $this->siteStore,
        ];
    }
}
