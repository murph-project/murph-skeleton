<?php

namespace App\Core\Site;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * class ControllerLocator.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class ControllerLocator
{
    protected array $params;
    protected array $controllers = [];

    public function __construct(ParameterBagInterface $bag)
    {
        $this->params = $bag->get('core');
        $this->loadControllers();
    }

    public function getControllers(): array
    {
        return $this->controllers;
    }

    protected function loadControllers(): void
    {
        $params = $this->params['site']['controllers'] ?? [];

        foreach ($params as $conf) {
            $configuration = new ControllerConfiguration();
            $configuration
                ->setName($conf['name'])
                ->setAction($conf['action'])
            ;

            $this->controllers[$conf['action']] = $configuration;
        }
    }
}
