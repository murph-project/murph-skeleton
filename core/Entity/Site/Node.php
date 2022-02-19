<?php

namespace App\Core\Entity\Site;

use App\Core\Doctrine\Timestampable;
use App\Core\Entity\EntityInterface;
use App\Core\Entity\NodeView;
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
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity=Menu::class, inversedBy="nodes", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $menu;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $label;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    protected $disableUrl = false;

    /**
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    protected $isVisible = false;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    protected $treeLeft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    protected $treeLevel;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    protected $treeRight;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Node")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    protected $treeRoot;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Node", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Node", mappedBy="parent")
     * @ORM\OrderBy({"treeLeft"="ASC"})
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity=Page::class, inversedBy="nodes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $page;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $code;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $parameters = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $attributes = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $controller;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $sitemapParameters = [];

    /**
     * @ORM\ManyToOne(targetEntity=Node::class, inversedBy="aliasNodes")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $aliasNode;

    /**
     * @ORM\OneToMany(targetEntity=Node::class, mappedBy="aliasNode")
     */
    protected $aliasNodes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $contentType;

    /**
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    private $enableViewCounter = false;

    /**
     * @ORM\OneToMany(targetEntity=NodeView::class, mappedBy="node", orphanRemoval=true)
     */
    private $nodeViews;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->aliasNodes = new ArrayCollection();
        $this->nodeViews = new ArrayCollection();
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
    public function getChildren(array $criteria = []): Collection
    {
        if (null === $this->children) {
            $this->children = new ArrayCollection();
        }

        if (!empty($criteria)) {
            $children = new ArrayCollection();

            foreach ($this->children as $child) {
                $add = true;

                if (isset($criteria['visible']) && $child->getIsVisible() !== $criteria['visible']) {
                    $add = false;
                }

                if ($add) {
                    $children->add($child);
                }
            }

            return $children;
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

    public function getAllChildren(array $criteria = []): ArrayCollection
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

        if (!empty($criteria)) {
            foreach ($children as $key => $child) {
                if (isset($criteria['visible']) && $child->getIsVisible() !== $criteria['visible']) {
                    unset($children[$key]);
                }
            }
        }

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

    public function hasAppUrl(): bool
    {
        $string = u($this->getUrl());

        foreach (['tel:', 'fax:', 'mailto:'] as $prefix) {
            if ($string->startsWith($prefix)) {
                return true;
            }
        }

        return false;
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
        if ($this->getAliasNode()) {
            return $this->getAliasNode()->getPage();
        }

        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getRouteName(): string
    {
        if ($this->getAliasNode()) {
            return $this->getAliasNode()->getRouteName();
        }

        return $this->getMenu()->getRouteName().'_'.($this->getCode() ? $this->getCode() : $this->getId());
    }

    public function getCode(): ?string
    {
        if ($this->getAliasNode()) {
            return $this->getAliasNode()->getCode();
        }

        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getParameters(): ?array
    {
        if ($this->getAliasNode()) {
            return $this->getAliasNode()->getParameters();
        }

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
        if ($this->getAliasNode()) {
            return $this->getAliasNode()->getController();
        }

        return $this->controller;
    }

    public function setController(?string $controller): self
    {
        $this->controller = $controller;

        return $this;
    }

    public function getSitemapParameters(): ?array
    {
        if ($this->getAliasNode()) {
            return $this->getAliasNode()->getSitemapParameters();
        }

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

    public function getAliasNode(): ?self
    {
        return $this->aliasNode;
    }

    public function setAliasNode(?self $aliasNode): self
    {
        $this->aliasNode = $aliasNode;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getAliasNodes(): Collection
    {
        return $this->aliasNodes;
    }

    public function addAliasNode(self $aliasNode): self
    {
        if (!$this->aliasNodes->contains($aliasNode)) {
            $this->aliasNodes[] = $aliasNode;
            $aliasNode->setAliasNode($this);
        }

        return $this;
    }

    public function removeAliasNode(self $aliasNode): self
    {
        if ($this->aliasNodes->removeElement($aliasNode)) {
            // set the owning side to null (unless already changed)
            if ($aliasNode->getAliasNode() === $this) {
                $aliasNode->setAliasNode(null);
            }
        }

        return $this;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(?string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function getEnableViewCounter(): ?bool
    {
        return $this->enableViewCounter;
    }

    public function setEnableViewCounter(bool $enableViewCounter): self
    {
        $this->enableViewCounter = $enableViewCounter;

        return $this;
    }

    /**
     * @return Collection|NodeView[]
     */
    public function getNodeViews(): Collection
    {
        return $this->nodeViews;
    }

    public function addNodeView(NodeView $nodeView): self
    {
        if (!$this->nodeViews->contains($nodeView)) {
            $this->nodeViews[] = $nodeView;
            $nodeView->setNode($this);
        }

        return $this;
    }

    public function removeNodeView(NodeView $nodeView): self
    {
        if ($this->nodeViews->removeElement($nodeView)) {
            // set the owning side to null (unless already changed)
            if ($nodeView->getNode() === $this) {
                $nodeView->setNode(null);
            }
        }

        return $this;
    }
}
