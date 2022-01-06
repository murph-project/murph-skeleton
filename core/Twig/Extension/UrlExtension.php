<?php

namespace App\Core\Twig\Extension;

use App\Core\Site\SiteRequest;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UrlExtension extends AbstractExtension
{
    protected UrlGeneratorInterface $urlGenerator;
    protected SiteRequest $siteRequest;

    public function __construct(UrlGeneratorInterface $urlGenerator, SiteRequest $siteRequest)
    {
        $this->urlGenerator = $urlGenerator;
        $this->siteRequest = $siteRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('murph_url', [$this, 'replaceUrl']),
        ];
    }

    public function replaceUrl(?string $content)
    {
        preg_match_all('#\{\{\s*url://(?P<route>[a-z0-9_]+)(\?(?P<params>.*))?\s*\}\}#isU', $content, $match, PREG_SET_ORDER);

        foreach ($match as $block) {
            $url = null;

            try {
                $block['params'] = $block['params'] ?? '';
                $block['params'] = str_replace(['&amp;', ' '], ['&', '%20'], $block['params']);
                $route = $block['route'];
                parse_str($block['params'], $params);

                if (!isset($params['_domain'])) {
                    $params['_domain'] = $this->siteRequest->getDomain();
                }

                $url = $this->urlGenerator->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);

                parse_str(parse_url($url)['query'] ?? '', $infos);

                if (isset($infos['_domain'])) {
                    unset($params['_domain']);

                    $url = $this->urlGenerator->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);
                }
            } catch (\Exception $e) {
            }

            $content = str_replace($block[0], $url, $content);
        }

        return $content;
    }
}
