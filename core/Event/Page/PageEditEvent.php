<?php

namespace App\Core\Event\Page;

use Symfony\Contracts\EventDispatcher\Event;
use App\Core\Entity\Site\Page\Page;

/**
 * class PageEditEvent.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class PageEditEvent extends Event
{
    const FORM_INIT_EVENT = 'page_edit_event.form_init';

    protected Page $page;
    protected array $pageBuilderOptions = [];

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getPageBuilderOptions(): array
    {
        return $this->pageBuilderOptions;
    }

    public function addPageBuilderOptions(array $options): self
    {
        $this->pageBuilderOptions = array_merge(
            $this->pageBuilderOptions,
            $options
        );

        return $this;
    }
}
