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
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity=Node::class, inversedBy="analyticViews")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $node;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $path;

    /**
     * @ORM\Column(type="integer", options={"default"=0})
     */
    protected $views = 0;

    /**
     * @ORM\Column(type="integer", options={"default"=0})
     */
    protected $desktopViews = 0;

    /**
     * @ORM\Column(type="integer", options={"default"=0})
     */
    protected $mobileViews = 0;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;

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

    public function getDesktopViews(): ?int
    {
        return $this->desktopViews;
    }

    public function setDesktopViews(int $desktopViews): self
    {
        $this->desktopViews = $desktopViews;

        return $this;
    }

    public function addDesktopView(): self
    {
        ++$this->desktopViews;

        return $this;
    }

    public function getMobileViews(): ?int
    {
        return $this->mobileViews;
    }

    public function setMobileViews(int $mobileViews): self
    {
        $this->mobileViews = $mobileViews;

        return $this;
    }

    public function addMobileView(): self
    {
        ++$this->mobileViews;

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
