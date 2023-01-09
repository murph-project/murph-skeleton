<?php

namespace App\Tests\Core\Site;

use App\Tests\Core\PantherTestCase;

/**
 * @internal
 * @coversNothing
 */
class NavigationTest extends PantherTestCase
{
    public function testCreateNavigation(): void
    {
        $this->authenticateAdmin();

        $this->client->request('GET', '/admin/site/tree');
        $this->client->waitFor('.toast-body.text-text-warning');
        $this->assertSelectorTextContains('.toast-body.text-text-warning', 'You must add a navigation.');

        $this->client->request('GET', '/admin/site/navigation');
        $this->assertSelectorTextContains('h1', 'Navigations');

        $this->client->request('GET', '/admin/site/navigation/new');
        $this->assertSelectorTextContains('h1', 'New navigation');
        $this->client->submitForm('Save', [
            'navigation[label]' => 'Test navigation',
            'navigation[locale]' => 'en',
            'navigation[code]' => 'nav',
            'navigation[domain]' => 'localhost',
        ]);

        $this->client->waitFor('.toast-body.text-text-success');
        $this->assertSelectorTextContains('.toast-body.text-text-success', 'The data has been saved.');

        $this->client->request('GET', '/admin/site/navigation');
        $this->assertSelectorTextContains('.table tbody tr td', 'Test navigation');
    }
}
