<?php

namespace App\Core\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

abstract class AdminController extends AbstractController
{
    protected array $coreParameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->coreParameters = $parameters->get('core');
    }

    /**
     * @Route("/_ping", name="_ping")
     */
    public function ping()
    {
        return $this->json(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $parameters['section'] = $this->getSection();
        $parameters['site_name'] = $this->coreParameters['site']['name'];
        $parameters['site_logo'] = $this->coreParameters['site']['logo'];
        $parameters['murph_version'] = defined('MURPH_VERSION') ? MURPH_VERSION : null;

        return parent::render($view, $parameters, $response);
    }

    abstract protected function getSection(): string;
}
