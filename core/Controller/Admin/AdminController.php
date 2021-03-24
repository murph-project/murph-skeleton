<?php

namespace App\Core\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class AdminController extends AbstractController
{
    protected array $coreParameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->coreParameters = $parameters->get('core');
    }

    /**
     * {@inheritdoc}
     */
    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $parameters['section'] = $this->getSection();
        $parameters['site_name'] = $this->coreParameters['site']['name'];
        $parameters['site_logo'] = $this->coreParameters['site']['logo'];

        return parent::render($view, $parameters, $response);
    }

    abstract protected function getSection(): string;
}
