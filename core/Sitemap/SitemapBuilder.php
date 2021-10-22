<?php

namespace App\Core\Sitemap;

use App\Core\Annotation\UrlGenerator;
use App\Core\Entity\Site\Navigation;
use App\Core\Entity\Site\Node;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * class SitemapBuilder.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class SitemapBuilder
{
    protected Reader $annotationReader;
    protected ContainerInterface $container;
    protected UrlGeneratorInterface $urlGenerator;

    public function __construct(Reader $annotationReader, ContainerInterface $container, UrlGeneratorInterface $urlGenerator)
    {
        $this->annotationReader = $annotationReader;
        $this->container = $container;
        $this->urlGenerator = $urlGenerator;
    }

    public function build(Navigation $navigation): array
    {
        $items = [];

        foreach ($navigation->getMenus() as $menu) {
            $rootNode = $menu->getRootNode();

            foreach ($rootNode->getAllChildren() as $node) {
                $parameters = $node->getSitemapParameters();

                if (!$parameters['isVisible']) {
                    continue;
                }

                if ($node->hasExternalUrl()) {
                    continue;
                }

                if ($node->getAliasNode()) {
                    continue;
                }

                $nodeItems = [];

                foreach ($this->getNodeUrls($node) as $url) {
                    $nodeItems[] = $this->createItem($parameters, $url);
                }

                $items = array_merge(
                    $items,
                    $nodeItems
                );
            }
        }

        return $items;
    }

    public function getNodeUrls(Node $node)
    {
        $urls = [];

        try {
            if ($node->hasExternalUrl()) {
                $urls[] = $node->getUrl();
            } elseif ($node->getController()) {
                $annotation = $this->getAnnotation($node);

                if (false !== $annotation) {
                    if (null === $annotation) {
                        $urls[] = $this->urlGenerator->generate(
                            $node->getRouteName(),
                            [],
                            UrlGeneratorInterface::ABSOLUTE_URL
                        );
                    } else {
                        $service = $this->container->get($annotation->service);
                        $method = $annotation->method;
                        $urls = $service->{$method}($node, $annotation->options);
                    }
                }
            } elseif (!$node->getDisableUrl() && !$node->hasAppUrl()) {
                $urls[] = $this->urlGenerator->generate(
                    $node->getRouteName(),
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
            }
        } catch (MissingMandatoryParametersException $e) {
        }

        return $urls;
    }

    protected function createItem(array $parameters, string $location): array
    {
        return array_merge(
            [
                'changefreq' => $parameters['changeFrequency'],
                'priority' => $parameters['priority'],
            ],
            ['loc' => $location]
        );
    }

    protected function getAnnotation(Node $node)
    {
        try {
            $annotation = $this->annotationReader->getMethodAnnotation(
                new \ReflectionMethod($node->getController()),
                UrlGenerator::class
            );

            if ($annotation) {
                return $annotation;
            }
        } catch (\ReflectionException $e) {
            return false;
        }

        return null;
    }
}
