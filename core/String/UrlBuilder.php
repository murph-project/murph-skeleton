<?php

namespace App\Core\String;

use App\Core\Site\SiteRequest;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * class UrlBuilder.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class UrlBuilder
{
    protected UrlGeneratorInterface $urlGenerator;
    protected SiteRequest $siteRequest;

    public function __construct(UrlGeneratorInterface $urlGenerator, SiteRequest $siteRequest)
    {
        $this->urlGenerator = $urlGenerator;
        $this->siteRequest = $siteRequest;
    }

    public function replaceTags(string $value): string
    {
        preg_match_all(
            '#\{\{\s*url://(?P<route>[a-z0-9_]+)(\?(?P<params>.*))?\s*\}\}#isU',
            $value,
            $match,
            PREG_SET_ORDER
        );

        $domain = $this->siteRequest->getDomain();

        foreach ($match as $block) {
            $url = null;

            try {
                $block['params'] = $block['params'] ?? '';
                $block['params'] = str_replace(['&amp;', ' '], ['&', '%20'], $block['params']);
                $route = $block['route'];
                parse_str($block['params'], $params);

                if (!isset($params['_domain'])) {
                    $params['_domain'] = $domain;
                }

                $url = $this->urlGenerator->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);

                parse_str(parse_url($url)['query'] ?? '', $infos);

                if (isset($infos['_domain'])) {
                    unset($params['_domain']);

                    $url = $this->urlGenerator->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);
                }
            } catch (\Exception $e) {
            }

            $value = str_replace($block[0], $url, $value);
        }

        return $value;
    }
}
