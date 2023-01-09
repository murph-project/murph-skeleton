<?php

namespace App\Tests\Core\Site;

use App\Tests\Core\PantherTestCase;

/**
 * @internal
 * @coversNothing
 */
class TreeTest extends PantherTestCase
{
    public function testCreateTree(): void
    {
        $this->client->request('GET', '/admin/site/tree');
        $this->assertSelectorTextContains('button[data-toggle="modal"]', 'Add a menu');
        $this->client->executeScript("document.querySelector('button[data-toggle=\"modal\"]').click()");

        $this->client->waitFor('#form-menu-new');
        $this->client->submitForm('Save', [
            'menu[label]' => 'Test menu',
            'menu[code]' => 'menu',
        ]);

        $this->client->waitFor('.toast-body.text-text-success');
        $this->assertSelectorTextContains('.toast-body.text-text-success', 'The data has been saved.');

        $this->client->request('GET', '/admin/site/tree');
        $this->assertSelectorTextContains('.h4', 'Test menu');
        $this->assertSelectorTextContains('#node-2 .col-6', 'First element');
    }
}
