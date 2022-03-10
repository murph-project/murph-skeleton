<?php

namespace App\Core\Site;

/**
 * class RoleConfiguration.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class RoleConfiguration
{
    protected string $name;
    protected string $role;

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
