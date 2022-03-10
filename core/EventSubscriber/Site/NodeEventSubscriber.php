<?php

namespace App\Core\EventSubscriber\Site;

use App\Core\Entity\EntityInterface;
use App\Core\Entity\Site\Node;
use App\Core\Event\EntityManager\EntityManagerEvent;
use App\Core\EventSubscriber\EntityManagerEventSubscriber;
use App\Core\Factory\Site\NodeFactory;
use App\Core\Manager\EntityManager;
use App\Core\Repository\Site\NodeRepository;
use App\Core\Slugify\CodeSlugify;
use App\Core\Slugify\RouteParameterSlugify;
use App\Core\Slugify\Slugify;
use Symfony\Component\HttpKernel\KernelInterface;
use function Symfony\Component\String\u;

/**
 * class NodeEventSubscriber.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class NodeEventSubscriber extends EntityManagerEventSubscriber
{
    protected NodeFactory $nodeFactory;
    protected EntityManager $entityManager;
    protected KernelInterface $kernel;
    protected Slugify $slugify;
    protected CodeSlugify $codeSlugify;
    protected RouteParameterSlugify $routeParameterSlugify;

    public function __construct(
        NodeFactory $nodeFactory,
        NodeRepository $nodeRepository,
        EntityManager $entityManager,
        Slugify $slugify,
        CodeSlugify $codeSlugify,
        RouteParameterSlugify $routeParameterSlugify
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->nodeRepository = $nodeRepository;
        $this->entityManager = $entityManager;
        $this->slugify = $slugify;
        $this->codeSlugify = $codeSlugify;
        $this->routeParameterSlugify = $routeParameterSlugify;
    }

    public function support(EntityInterface $entity)
    {
        return $entity instanceof Node;
    }

    public function onPreCreate(EntityManagerEvent $event)
    {
        return $this->onPreUpdate($event);
    }

    public function onPreUpdate(EntityManagerEvent $event)
    {
        if (!$this->support($event->getEntity())) {
            return;
        }

        $node = $event->getEntity();

        $node->setCode($this->codeSlugify->slugify($node->getCode()));

        if ($node->getDisableUrl()) {
            $node->setUrl(null);
        } else {
            if ($node->getUrl()) {
                $generatedUrl = $node->getUrl();
            } else {
                $path = [];
                $parent = $node->getParent();

                if ($parent && $parent->getUrl()) {
                    $pPath = trim($parent->getUrl(), '/');

                    if ($pPath) {
                        $path[] = $pPath;
                    }
                }

                $path[] = $this->slugify->slugify($node->getLabel());

                $generatedUrl = '/'.implode('/', $path);
            }

            if ('/' !== $generatedUrl) {
                $generatedUrl = rtrim($generatedUrl, '/');
            }

            $parameters = $node->getParameters();
            $routeParameters = [];

            foreach ($parameters as $key => $parameter) {
                $parameter['name'] = $this->routeParameterSlugify->slugify($parameter['name']);
                $routeParameter = sprintf('{%s}', $parameter['name']);
                $regex = '/'.preg_quote($routeParameter).'/';
                $routeParameters[] = $parameter['name'];

                if (!preg_match($regex, $generatedUrl)) {
                    $generatedUrl .= '/'.$routeParameter;
                }

                $parameters[$key] = $parameter;
            }

            preg_match_all('/\{(.*)\}/isU', $generatedUrl, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                if (!in_array($match[1], $routeParameters)) {
                    $parameters[] = [
                        'name' => $this->routeParameterSlugify->slugify($match[1]),
                        'defaultValue' => null,
                        'requirement' => null,
                    ];
                }
            }

            $node->setParameters($parameters);

            $urlExists = $this->nodeRepository->urlExists($generatedUrl, $node);

            if ($urlExists) {
                $number = 1;

                while ($this->nodeRepository->urlExists($generatedUrl.'-'.$number, $node)) {
                    ++$number;
                }

                $generatedUrl = $generatedUrl.'-'.$number;
            }

            if (
                !u($generatedUrl)->startsWith('https://')
                && !u($generatedUrl)->startsWith('http://')
                && !u($generatedUrl)->startsWith('tel:')
                && !u($generatedUrl)->startsWith('mailto:')
                && !u($generatedUrl)->startsWith('fax:')
            ) {
                $generatedUrl = '/'.$generatedUrl;
                $generatedUrl = preg_replace('#/{2,}#', '/', $generatedUrl);
            }

            $node->setUrl($generatedUrl);
        }

        $attributes = $node->getAttributes();
        $realAttributes = [];

        foreach ($attributes as $key => $attribute) {
            $realAttributes[$this->routeParameterSlugify->slugify($attribute['label'])] = $attribute;
        }

        $node->setAttributes($realAttributes);
    }

    public function onDelete(EntityManagerEvent $event)
    {
        if (!$this->support($event->getEntity())) {
            return;
        }

        $menu = $event->getEntity()->getMenu();
        $rootNode = $menu->getRootNode();

        if (0 !== count($rootNode->getChildren())) {
            return;
        }

        $childNode = $this->nodeFactory->create($menu);
        $childNode
            ->setParent($rootNode)
            ->setLabel('Premier élément')
        ;

        $this->entityManager->update($rootNode, false);
        $this->entityManager->create($childNode, false);
        $this->nodeRepository->persistAsFirstChild($childNode, $rootNode);
    }
}
