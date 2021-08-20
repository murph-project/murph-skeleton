<?php

namespace App\Core\Router;

use App\Core\Controller\Site\PageController;
use App\Core\Repository\Site\NavigationRepositoryQuery;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * class SiteRouteLoader.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class SiteRouteLoader extends Loader
{
    protected NavigationRepositoryQuery $navigationQuery;
    protected $isLoaded = false;

    public function __construct(NavigationRepositoryQuery $navigationQuery)
    {
        $this->navigationQuery = $navigationQuery;
    }

    public function load($resource, ?string $type = null)
    {
        if (true === $this->isLoaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }

        $routes = new RouteCollection();
        $navigations = $this->navigationQuery->find();

        $uniqueness = [];

        foreach ($navigations as $navigation) {
            if (!isset($uniqueness[$navigation->getDomain()])) {
                $uniqueness[$navigation->getDomain()] = true;
            } else {
                $uniqueness[$navigation->getDomain()] = false;
            }
        }

        foreach ($navigations as $navigation) {
            foreach ($navigation->getMenus() as $menu) {
                foreach ($menu->getRootNode()->getAllChildren() as $node) {
                    if (null === $node->getParent()) {
                        continue;
                    }

                    if (null === $node->getUrl()) {
                        continue;
                    }

                    if ($node->hasExternalUrl()) {
                        continue;
                    }

                    if ($node->hasAppUrl()) {
                        continue;
                    }

                    if (null !== $node->getAliasNode()) {
                        continue;
                    }

                    $requirements = [];

                    $defaults = [
                        '_controller' => $node->getController() ?? PageController::class.'::show',
                        '_locale' => $navigation->getLocale(),
                        '_node' => $node->getId(),
                        '_menu' => $menu->getId(),
                        '_page' => $node->getPage() ? $node->getPage()->getId() : null,
                        '_navigation' => $navigation->getId(),
                        '_domain' => $navigation->getDomain(),
                    ];

                    foreach ($node->getParameters() as $parameter) {
                        $name = $parameter['name'];

                        if (null !== $parameter['requirement']) {
                            $requirements[$name] = $parameter['requirement'];
                        }

                        if (null !== $parameter['defaultValue']) {
                            $defaults[$name] = $parameter['defaultValue'];
                        }
                    }

                    $requirements['_locale'] = $navigation->getLocale();

                    $additionalDomains = $navigation->getAdditionalDomains();

                    if (count($additionalDomains)) {
                        $host = '{_domain}';
                        $domains = [
                            preg_quote($navigation->getDomain()),
                        ];

                        foreach ($additionalDomains as $additionalDomain) {
                            if ('domain' === $additionalDomain['type']) {
                                $domains[] = preg_quote($additionalDomain['domain']);
                            } elseif ('regexp' === $additionalDomain['type']) {
                                $domains[] = $additionalDomain['domain'];
                            }
                        }

                        $requirements['_domain'] = sprintf('(%s)', implode('|', $domains));
                    } else {
                        $host = $navigation->getDomain();
                    }

                    $url = $node->getUrl();

                    if (!$uniqueness[$navigation->getDomain()]) {
                        $url = sprintf('/%s%s', $navigation->getLocale(), $url);
                    }

                    $route = new Route($url, $defaults, $requirements, [], $host);

                    $routes->add($node->getRouteName(), $route);
                }
            }
        }

        $this->isLoaded = true;

        return $routes;
    }

    public function supports($resource, string $type = null)
    {
        return 'extra' === $type;
    }
}
