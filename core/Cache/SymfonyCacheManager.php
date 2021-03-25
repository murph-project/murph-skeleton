<?php

namespace App\Core\Cache;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * class SymfonyCacheManager.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class SymfonyCacheManager
{
    protected KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function cleanRouting()
    {
        $finder = new Finder();
        $finder
            ->in($this->kernel->getCacheDir())
            ->depth('== 0')
            ->name('url_*.php*')
        ;

        foreach ($finder as $file) {
            unlink((string) $file->getPathname());
        }
    }

    public function cleanAll()
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'cache:clear',
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);
    }
}
