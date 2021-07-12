<?php

namespace App\Core\Entity;

use App\Repository\Entity\FileInformationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileInformationRepository::class)
 */
class FileInformation implements EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string", length=96, unique=true)
     */
    protected $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $attributes;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getAttributes()
    {
        return (array) json_decode($this->attributes, true);
    }

    public function setAttributes($attributes): self
    {
        $this->attributes = json_encode($attributes);

        return $this;
    }
}
