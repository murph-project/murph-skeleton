<?php

namespace App\Core\Slugify;

use Cocur\Slugify\Slugify as BaseSlugify;

/**
 * class CodeSlugify.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class RouteParameterSlugify extends Slugify
{
    public function slugify($data): ?string
    {
        $slug = parent::slugify($data);

        return preg_replace('/[^\w]+/', '', $slug);
    }

    protected function create(): BaseSlugify
    {
        $slugify = new BaseSlugify([
            'separator' => '_',
            'lowercase' => false,
        ]);

        $slugify->activateRuleSet('french');

        return $slugify;
    }
}
