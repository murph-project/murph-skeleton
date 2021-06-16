<?php

namespace App\Core\Entity\Site\Page;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CollectionBlock extends Block
{
    public function getValue()
    {
        return json_decode(parent::getValue(), true);
    }

    public function setValue($value): self
    {
        return parent::setValue(json_encode($value));
    }
}
