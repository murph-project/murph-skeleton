<?php

namespace App\Core\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class BlockExtension extends AbstractExtension
{
    protected UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
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

    public function replaceUrl($content)
    {
        preg_match_all('#\{\{\s*url://(?P<route>[a-z_]+)(\?(?P<params>.*))?\s*\}\}#isU', $content, $match, PREG_SET_ORDER);

        foreach ($match as $block) {
            $url = null;

            try {
                $block['params'] = $block['params'] ?? '';
                $block['params'] = str_replace(['&amp;', ' '], ['&', '%20'], $block['params']);
                $route = $block['route'];
                parse_str($block['params'], $params);

                $url = $this->urlGenerator->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);
            } catch (\Exception $e) {
            }

            $content = str_replace($match[0], $url, $content);
        }

        return $content;
    }
}
