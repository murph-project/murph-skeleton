<?php

namespace App\Core\Twig\Extension;

use App\Core\FileManager\FsFileManager;
use App\Core\Repository\FileInformationRepositoryQuery;
use function Symfony\Component\String\u;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FileInformationExtension extends AbstractExtension
{
    protected FsFileManager $fsManage²r;
    protected FileInformationRepositoryQuery $query;

    public function __construct(FsFileManager $fsManager, FileInformationRepositoryQuery $query)
    {
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

    public function fileAttributes(?string $content): ?string
    {
        preg_match_all('#\{\{\s*fattr://(?P<hash>[a-z0-9]+)\/(?P<label>.+)\s*\}\}#isU', $content, $match, PREG_SET_ORDER);

        foreach ($match as $block) {
            $hash = $block['hash'];
            $label = $block['label'];
            $value = null;

            $fileInfo = $this->query->create()
                ->where('.id LIKE :hash')
                ->setParameter(':hash', $hash.'%')
                ->findOne()
            ;

            if ($fileInfo) {
                foreach ($fileInfo->getAttributes() as $attribute) {
                    if ($attribute['label'] === $label) {
                        $value = htmlspecialchars($attribute['value'], ENT_HTML5 | ENT_QUOTES);
                    }
                }
            }

            $content = str_replace($block[0], $value, $content);
        }

        return $content;
    }
}
