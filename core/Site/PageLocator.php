<?php

namespace App\Core\Site;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * class PageLocator.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class PageLocator
{
    protected array $params;
    protected array $pages = [];

    public function __construct(ParameterBagInterface $bag)
    {
        $this->params = $bag->get('core');
        $this->loadPages();
    }

    public function getPages(): array
    {
        return $this->pages;
    }

    public function getPage($className)
    {
        return $this->pages[$className] ?? null;
    }

    protected function loadPages(): void
    {
        $params = $this->params['site']['pages'] ?? [];

        foreach ($params as $className => $conf) {
            $pageConfiguration = new PageConfiguration();
            $pageConfiguration
                ->setClassName($className)
                ->setName($conf['name'])
                ->setTemplates($conf['templates'])
            ;

            $this->pages[$className] = $pageConfiguration;
        }
    }
}
