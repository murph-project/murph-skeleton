<?php

namespace App\Core\Crud;

/**
 * class CrudConfiguration.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class CrudConfiguration
{
    protected array $pageTitles = [];
    protected array $pageRoutes = [];
    protected array $actions = [];
    protected array $actionTitles = [];
    protected array $forms = [];
    protected array $formOptions = [];
    protected array $views = [];
    protected array $fields = [];

    /* -- */

    public function setPageTitle(string $page, string $title): self
    {
        $this->pageTitles[$page] = $title;

        return $this;
    }

    public function getPageTitle(string $page, ?string $default = null): ?string
    {
        return $this->pageTitles[$page] ?? $default;
    }

    /* -- */

    public function setPageRoute(string $page, string $route): self
    {
        $this->pageRoutes[$page] = $route;

        return $this;
    }

    public function getPageRoute(string $page): ?string
    {
        return $this->pageRoutes[$page];
    }

    /* -- */

    public function setForm(string $context, string $form, array $options = []): self
    {
        $this->forms[$context] = $form;

        return $this;
    }

    public function getForm(string $context): string
    {
        return $this->forms[$context];
    }

    public function setFormOptions(string $context, array $options = []): self
    {
        $this->formOptions[$context] = $options;

        return $this;
    }

    public function getFormOptions(string $context): array
    {
        return $this->formOptions[$context] ?? [];
    }

    /* -- */

    public function setAction(string $page, string $action, bool $enabled): self
    {
        if (!isset($this->actions[$page])) {
            $this->actions[$page] = [];
        }

        $this->actions[$page][$action] = $enabled;

        return $this;
    }

    public function getAction(string $page, string $action, bool $default = true)
    {
        return $this->actions[$page][$action] ?? $default;
    }

    /* -- */

    public function setActionTitle(string $page, string $action, string $title): self
    {
        if (!isset($this->actionTitles[$page])) {
            $this->actionTitles[$page] = [];
        }

        $this->actions[$page][$action] = $title;

        return $this;
    }

    public function getActionTitle(string $page, string $action, ?string $default = null): ?string
    {
        return $this->actionTitles[$page][$action] ?? $default;
    }

    /* -- */

    public function setView(string $context, string $view): self
    {
        $this->views[$context] = $view;

        return $this;
    }

    public function getView(string $context, ?string $default = null)
    {
        if (null === $default) {
            $default = sprintf('@Core/admin/crud/%s.html.twig', $context);
        }

        return $this->views[$context] ?? $default;
    }

    /* -- */

    public function setField(string $context, string $label, string $field, array $options): self
    {
        if (!isset($this->fields[$context])) {
            $this->fields[$context] = [];
        }

        $this->fields[$context][$label] = [
            'field' => $field,
            'options' => $options,
        ];

        return $this;
    }

    public function getFields(string $context): array
    {
        return $this->fields[$context] ?? [];
    }
}
