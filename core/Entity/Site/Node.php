<?php

namespace App\Core\Entity\Site;

use App\Core\Doctrine\Timestampable;
use App\Core\Entity\Analytic\Referer;
use App\Core\Entity\Analytic\View;
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
    protected $enableAnalytics = false;

    /**
     * @ORM\OneToMany(targetEntity=View::class, mappedBy="node")
     */
    protected $analyticViews;

    /**
     * @ORM\OneToMany(targetEntity=Referer::class, mappedBy="node")
     */
    protected $analyticReferers;

    /**
     * @ORM\Column(type="array")
     */
    private $securityRoles = [];

    /**
     * @ORM\Column(type="string", length=3, options={"default"="or"})
     */
    private $securityOperator = 'or';

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->aliasNodes = new ArrayCollection();
        $this->analyticViews = new ArrayCollection();
        $this->analyticReferers = new ArrayCollection();
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

    public function getEnableAnalytics(): ?bool
    {
        return $this->enableAnalytics;
    }

    public function setEnableAnalytics(bool $enableAnalytics): self
    {
        $this->enableAnalytics = $enableAnalytics;

        return $this;
    }

    /**
     * @return Collection|View[]
     */
    public function getAnalyticViews(): Collection
    {
        return $this->analyticViews;
    }

    public function addAnalyticView(View $view): self
    {
        if (!$this->analyticViews->contains($view)) {
            $this->analyticViews[] = $view;
            $view->setNode($this);
        }

        return $this;
    }

    public function removeAnalyticView(View $view): self
    {
        if ($this->analyticViews->removeElement($view)) {
            // set the owning side to null (unless already changed)
            if ($view->getNode() === $this) {
                $view->setNode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Referer[]
     */
    public function getAnalyticReferers(): Collection
    {
        return $this->analyticReferers;
    }

    public function addAnalyticReferer(Referer $referer): self
    {
        if (!$this->analyticReferers->contains($referer)) {
            $this->analyticReferers[] = $referer;
            $referer->setNode($this);
        }

        return $this;
    }

    public function removeAnalyticReferer(Referer $referer): self
    {
        if ($this->analyticReferers->removeElement($referer)) {
            // set the owning side to null (unless already changed)
            if ($referer->getNode() === $this) {
                $referer->setNode(null);
            }
        }

        return $this;
    }

    public function getSecurityRoles(): array
    {
        return !is_array($this->securityRoles) ? [] : $this->securityRoles;
    }

    public function setSecurityRoles(array $securityRoles): self
    {
        $this->securityRoles = $securityRoles;

        return $this;
    }

    public function getSecurityOperator(): ?string
    {
        return $this->securityOperator;
    }

    public function setSecurityOperator(string $securityOperator): self
    {
        $this->securityOperator = $securityOperator;

        return $this;
    }
}
