<?php

namespace App\Tests\Core\Site;

use App\Tests\Core\PantherTestCase;

/**
 * @internal
 * @coversNothing
 */
class PageTest extends PantherTestCase
{
    public function testCreatePage(): void
    {
        $this->client->request('GET', '/admin/site/tree');
        $this->client->executeScript("document.querySelector('#node-2 .float-right button[data-modal]').click()");

        $this->client->waitFor('#form-node-edit');
        $this->client->executeScript("document.querySelector('#node-page-action .card-header label').click()");
        $this->client->executeScript("document.querySelector('a[href=\"#form-node-edit-routing\"]').click()");
        $this->client->executeScript("document.querySelector('#node_url').value='/foo'");
        $this->client->executeScript("document.querySelector('#node_code').value='/foo'");
        $this->client->executeScript("document.querySelector('.modal.show .modal-footer button[type=\"submit\"]').click()");

        $this->client->waitFor('.toast-body.text-text-success');
        $this->assertSelectorTextContains('.toast-body.text-text-success', 'The data has been saved.');

        $this->assertSelectorTextContains('#node-2 .float-right a', 'Page');
    }
}
