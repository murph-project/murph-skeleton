<?php

namespace App\Core\Site;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * class RoleLocator.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class RoleLocator
{
    protected array $params;
    protected array $roles = [];

    public function __construct(ParameterBagInterface $bag)
    {
        $this->params = $bag->get('core');
        $this->loadRoles();
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    protected function loadRoles(): void
    {
        $params = $this->params['site']['security']['roles'] ?? [];

        foreach ($params as $conf) {
            $configuration = new RoleConfiguration();
            $configuration
                ->setName($conf['name'])
                ->setRole($conf['role'])
            ;

            $this->roles[$conf['name']] = $configuration;
        }
    }
}
