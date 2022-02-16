<?php

namespace App\Core\String;

use App\Core\FileManager\FsFileManager;
use App\Core\Repository\FileInformationRepositoryQuery;

/**
 * class FileInformationBuilder.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class FileInformationBuilder
{
    protected FsFileManager $fsManager;
    protected FileInformationRepositoryQuery $query;

    public function __construct(FsFileManager $fsManager, FileInformationRepositoryQuery $query)
    {
        $this->fsManager = $fsManager;
        $this->query = $query;
    }

    public function replaceTags(string $value)
    {
        preg_match_all(
            '#\{\{\s*fattr://(?P<hash>[a-z0-9]+)\/(?P<label>.+)\s*\}\}#isU',
            $value,
            $match,
            PREG_SET_ORDER
        );

        $fileInfos = [];

        foreach ($match as $block) {
            $hash = $block['hash'];
            $label = $block['label'];
            $tagValue = null;

            if (!isset($fileInfos[$hash])) {
                $fileInfos[$hash] = $this->query->create()
                    ->where('.id LIKE :hash')
                    ->setParameter(':hash', $hash.'%')
                    ->findOne()
                ;
            }

            if ($fileInfos[$hash]) {
                foreach ($fileInfos[$hash]->getAttributes() as $attribute) {
                    if ($attribute['label'] === $label) {
                        $tagValue = htmlspecialchars($attribute['value'], ENT_HTML5 | ENT_QUOTES);
                    }
                }
            }

            $value = str_replace($block[0], $tagValue, $value);
        }

        return $value;
    }
}
