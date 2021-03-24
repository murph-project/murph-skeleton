<?php

namespace App\Core\Slugify;

use Cocur\Slugify\Slugify as BaseSlugify;

/**
 * class Slugify.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class Slugify
{
    public function slugify($data): ?string
    {
        return $this->create()->slugify($data);
    }

    protected function create(): BaseSlugify
    {
        $slugify = new BaseSlugify([
            'separator' => '-',
            'lowercase' => true,
        ]);

        $slugify->activateRuleSet('french');
        $slugify->addRule("'", '');

        return $slugify;
    }
}
