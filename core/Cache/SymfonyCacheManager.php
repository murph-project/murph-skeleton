<?php

namespace App\Core\Cache;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * class SymfonyCacheManager.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class SymfonyCacheManager
{
    protected KernelInterface $kernel;
    protected HttpClientInterface $httpClient;
    protected UrlGeneratorInterface $urlGenerator;

    public function __construct(KernelInterface $kernel, HttpClientInterface $httpClient, UrlGeneratorInterface $urlGenerator)
    {
        $this->kernel = $kernel;
        $this->httpClient = $httpClient;
        $this->urlGenerator = $urlGenerator;
    }

    public function cleanRouting()
    {
        $finder = new Finder();
        $finder
            ->in($this->kernel->getCacheDir())
            ->depth('== 0')
            ->name('url_*.php*')
        ;

        $pingUrl = $this->urlGenerator->generate('_ping', [], UrlGeneratorInterface::ABSOLUTE_URL);

        foreach ($finder as $file) {
            unlink((string) $file->getPathname());
        }

        // Hack: used to regenerate cache of url generator
        $this->httpClient->request('POST', $pingUrl);
    }

    public function cleanAll()
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);
        $output = new BufferedOutput();

        $input = new ArrayInput([
            'command' => 'cache:clear',
            '-e' => $this->kernel->getEnvironment(),
            '--no-warmup' => null,
        ]);

        $application->run($input, $output);

        $input = new ArrayInput([
            'command' => 'cache:warmup',
            '-e' => $this->kernel->getEnvironment(),
        ]);

        $application->run($input, $output);
    }
}
