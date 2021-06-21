<?php

namespace App\Core\Entity\Site\Page;

use App\Core\File\FileAttribute;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity
 */
class FileBlock extends Block
{
    public function getValue()
    {
        return FileAttribute::handleFile(parent::getValue());
    }

    public function setValue($value): self
    {
        if ($this->getValue() instanceof File && null === $value) {
            return $this;
        }

        return parent::setValue($value);
    }
}
