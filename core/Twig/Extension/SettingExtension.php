<?php

namespace App\Core\Twig\Extension;

use App\Core\Setting\SettingManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingExtension extends AbstractExtension
{
    private SettingManager $manager;

    public function __construct(SettingManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('setting', [$this, 'getSetting']),
        ];
    }

    public function getSetting(string $code)
    {
        $entity = $this->manager->get($code);

        return $entity ? $entity->getValue() : null;
    }
}
