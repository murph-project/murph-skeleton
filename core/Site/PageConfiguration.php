<?php

namespace App\Core\Site;

/**
 * class PageConfiguration.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class PageConfiguration
{
    protected string $className;
    protected string $name;
    protected array $templates;

    public function setClassName(string $className): self
    {
        $this->className = $className;

        return $this;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setTemplates(array $templates): self
    {
        $this->templates = $templates;

        return $this;
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }
}
