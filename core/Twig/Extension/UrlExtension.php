<?php

namespace App\Core\Twig\Extension;

use App\Core\Site\SiteRequest;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use App\Core\String\UrlBuilder;

class UrlExtension extends AbstractExtension
{
    protected UrlBuilder $urlBuilder;

    public function __construct(UrlBuilder $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
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
        return $this->urlBuilder->replaceTags((string) $content);
    }
}
