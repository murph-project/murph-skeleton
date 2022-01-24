<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Core\Bundle;

use App\Core\DependencyInjection\CoreExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class CoreBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new CoreExtension();
    }
}
