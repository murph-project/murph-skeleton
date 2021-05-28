<?php

namespace App\Core\Twig\Extension;

use App\Core\Setting\SettingManager;
use App\Core\Setting\NavigationSettingManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingExtension extends AbstractExtension
{
    private SettingManager $settingManager;
    private NavigationSettingManager $navigationSettingManager;

    public function __construct(SettingManager $settingManager, NavigationSettingManager $navigationSettingManager)
    {
        $this->settingManager = $settingManager;
        $this->navigationSettingManager = $navigationSettingManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('setting', [$this, 'getSetting']),
            new TwigFunction('navigation_setting', [$this, 'getNavigationSetting']),
        ];
    }

    public function getSetting(string $code)
    {
        $entity = $this->settingManager->get($code);

        return $entity ? $entity->getValue() : null;
    }

    public function getNavigationSetting($navigation, string $code)
    {
        $entity = $this->navigationSettingManager->get($navigation, $code);

        return $entity ? $entity->getValue() : null;
    }
}
