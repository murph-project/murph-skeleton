<?php

namespace App\Core\Entity\Site;

use App\Core\Doctrine\Timestampable;
use App\Core\Entity\EntityInterface;
use App\Core\Entity\Site\Page\Page;
use App\Core\Repository\Site\NodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use function Symfony\Component\String\u;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass=NodeRepository::class)
 */
class Node implements EntityInterface
{
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Menu::class, inversedBy="nodes", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $menu;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    private $disableUrl = false;

    /**
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    private $isVisible = false;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    private $treeLeft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    private $treeLevel;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    private $treeRight;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Node")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $treeRoot;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Node", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Node", mappedBy="parent")
     * @ORM\OrderBy({"treeLeft"="ASC"})
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity=Page::class, inversedBy="nodes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $page;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $parameters = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $attributes = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $controller;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $sitemapParameters = [];

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): self
    {
        $this->menu = $menu;

        return $this;
    }

    public function getTreeLeft(): ?int
    {
        return $this->treeLeft;
    }

    public function setTreeLeft(int $treeLeft): self
    {
        $this->treeLeft = $treeLeft;

        return $this;
    }

    public function getTreeLevel(): ?int
    {
        return $this->treeLevel;
    }

    public function setTreeLevel(int $treeLevel): self
    {
        $this->treeLevel = $treeLevel;

        return $this;
    }

    public function getTreeRight(): ?int
    {
        return $this->treeRight;
    }

    public function setTreeRight(int $treeRight): self
    {
        $this->treeRight = $treeRight;

        return $this;
    }

    public function getTreeRoot(): ?self
    {
        return $this->treeRoot;
    }

    public function setTreeRoot(?self $treeRoot): self
    {
        $this->treeRoot = $treeRoot;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|Node[]
     */
    public function getChildren(): Collection
    {
        if (null === $this->children) {
            $this->children = new ArrayCollection();
        }

        return $this->children;
    }

    public function addChild(Node $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Node $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getAllChildren(): ArrayCollection
    {
        $children = [];

        $getChildren = function (Node $node) use (&$children, &$getChildren) {
            foreach ($node->getChildren() as $nodeChildren) {
                $children[] = $nodeChildren;

                $getChildren($nodeChildren);
            }
        };

        $getChildren($this);

        usort($children, function ($a, $b) {
            return $a->getTreeLeft() < $b->getTreeLeft() ? -1 : 1;
        });

        return new ArrayCollection($children);
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function hasExternalUrl(): bool
    {
        $string = u($this->getUrl());

        return $string->startsWith('http://') || $string->startsWith('https://');
    }

    public function getIsVisible(): ?bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): self
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    public function getDisableUrl(): ?bool
    {
        return $this->disableUrl;
    }

    public function setDisableUrl(bool $disableUrl): self
    {
        $this->disableUrl = $disableUrl;

        return $this;
    }

    public function getTreeLabel()
    {
        $prefix = str_repeat('-', ($this->getTreeLevel() - 1) * 5);

        return trim($prefix.' '.$this->getLabel());
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getRouteName(): string
    {
        return $this->getMenu()->getRouteName().'_'.($this->getCode() ? $this->getCode() : $this->getId());
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getParameters(): ?array
    {
        if (!is_array($this->parameters)) {
            $this->parameters = [];
        }

        return $this->parameters;
    }

    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getAttributes(): ?array
    {
        if (!is_array($this->attributes)) {
            $this->attributes = [];
        }

        return $this->attributes;
    }

    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getController(): ?string
    {
        return $this->controller;
    }

    public function setController(?string $controller): self
    {
        $this->controller = $controller;

        return $this;
    }

    public function getSitemapParameters(): ?array
    {
        if (!is_array($this->sitemapParameters)) {
            $this->sitemapParameters = [
                'isVisible' => false,
                'priority' => 0,
                'changeFrequency' => 'daily',
            ];
        }

        return $this->sitemapParameters;
    }

    public function setSitemapParameters(?array $sitemapParameters): self
    {
        $this->sitemapParameters = $sitemapParameters;

        return $this;
    }
}
