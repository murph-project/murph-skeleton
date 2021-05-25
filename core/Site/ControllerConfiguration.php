<?php

namespace App\Core\Site;

/**
 * class ControllerConfiguration.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class ControllerConfiguration
{
    protected string $name;
    protected string $action;

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}
