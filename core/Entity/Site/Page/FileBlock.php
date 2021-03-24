<?php

namespace App\Core\Entity\Site\Page;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity
 */
class FileBlock extends Block
{
    public function getValue()
    {
        $value = parent::getValue();

        if (is_string($value)) {
            if (file_exists($value)) {
                return new File($value);
            }

            return null;
        }

        return $value;
    }

    public function setValue($value): self
    {
        if ($this->getValue() instanceof File && null === $value) {
            return $this;
        }

        return parent::setValue($value);
    }
}
