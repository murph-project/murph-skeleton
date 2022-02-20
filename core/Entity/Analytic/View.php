<?php

namespace App\Core\Entity\Analytic;

use App\Core\Entity\Site\Node;
use App\Repository\Entity\Analytic\NodeViewRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Core\Entity\EntityInterface;

/**
 * @ORM\Entity(repositoryClass=ViewRepository::class)
 * @ORM\Table(name="analytic_view")
 */
class View implements EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Node::class, inversedBy="nodeViews")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $node;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="integer", options={"default"=0})
     */
    private $views = 0;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNode(): ?Node
    {
        return $this->node;
    }

    public function setNode(?Node $node): self
    {
        $this->node = $node;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }

    public function addView(): self
    {
        ++$this->views;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
