<?php

namespace App\Core\Twig\Extension;

use App\Core\FileManager\FsFileManager;
use App\Core\Repository\FileInformationRepositoryQuery;
use function Symfony\Component\String\u;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use App\Core\String\FileInformationBuilder;

class FileInformationExtension extends AbstractExtension
{
    protected FileInformationBuilder $fileInfoBuilder;

    public function __construct(
        FileInformationBuilder $fileInfoBuilder,
        FsFileManager $fsManager,
        FileInformationRepositoryQuery $query
    )
    {
        $this->fileInfoBuilder = $fileInfoBuilder;
        $this->fsManager = $fsManager;
        $this->query = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('file_attribute', [$this, 'fileAttribute']),
            new TwigFilter('file_attributes', [$this, 'fileAttributes']),
        ];
    }

    public function fileAttribute(string $file, string $label): ?string
    {
        $file = u($file);
        $pathUri = $this->fsManager->getPathUri();
        $pathUri2 = '/'.$pathUri;

        if ($file->startsWith($pathUri) || $file->startsWith($pathUri2)) {
            $file = $file->replaceMatches('#^'.preg_quote($pathUri).'#', '');
            $file = $file->replaceMatches('#^'.preg_quote($pathUri2).'#', '');
        }

        $fileInfo = $this->fsManager->getFileInformation((string) $file);

        if ($fileInfo) {
            foreach ($fileInfo->getAttributes() as $attribute) {
                if ($attribute['label'] === $label) {
                    return $attribute['value'];
                }
            }
        }

        return null;
    }

    public function fileAttributes(?string $content): string
    {
        return $this->fileInfoBuilder->replaceTags((string) $content);
    }
}
