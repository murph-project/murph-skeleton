<?php

namespace App\Core\Router;

use App\Core\Entity\Redirect;

/**
 * class RedirectMatcher.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class RedirectMatcher
{
    public function match(Redirect $redirect, $url): bool
    {
        $data = $this->parse($url);

        if (!$this->matchSchema($redirect->getScheme(), $data['scheme'])) {
            return false;
        }

        if (!$this->matchDomain($redirect->getDomain(), $redirect->getDomainType(), $data['host'])) {
            return false;
        }

        if (!$this->matchRule($redirect->getRule(), $redirect->getRuleType(), $data['path'])) {
            return false;
        }

        return true;
    }

    protected function matchSchema(string $redirectScheme, $scheme): bool
    {
        if ('all' === $redirectScheme) {
            return true;
        }

        return $redirectScheme === $scheme;
    }

    protected function matchDomain(string $domain, string $type, string $host): bool
    {
        if ('domain' === $type) {
            return $domain === $host;
        }

        return preg_match('`'.$domain.'`', $host) > 0;
    }

    protected function matchRule(string $rule, string $type, string $path): bool
    {
        if ('path' === $type) {
            return $rule === $path;
        }

        return preg_match('`'.$rule.'`', $path) > 0;
    }

    protected function parse($url): array
    {
        return parse_url($url);
    }
}
