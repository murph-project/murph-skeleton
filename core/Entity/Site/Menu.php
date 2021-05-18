<?php

namespace App\Core\Entity\Site;

use App\Core\Doctrine\Timestampable;
use App\Core\Entity\EntityInterface;
use App\Core\Repository\Site\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Menu implements EntityInterface
{
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $label;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $code;

    /**
     * @ORM\ManyToOne(targetEntity=Navigation::class, inversedBy="menus")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $navigation;

    /**
     * @ORM\OneToMany(targetEntity=Node::class, mappedBy="menu", orphanRemoval=true, cascade={"remove", "persist"})
     */
    protected $nodes;

    /**
     * @ORM\OneToOne(targetEntity=Node::class, cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $rootNode;

    public function __construct()
    {
        $this->nodes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getNavigation(): ?Navigation
    {
        return $this->navigation;
    }

    public function setNavigation(?Navigation $navigation): self
    {
        $this->navigation = $navigation;

        return $this;
    }

    /**
     * @return Collection|Node[]
     */
    public function getNodes(): Collection
    {
        return $this->nodes;
    }

    public function addNode(Node $node): self
    {
        if (!$this->nodes->contains($node)) {
            $this->nodes[] = $node;
            $node->setMenu($this);
        }

        return $this;
    }

    public function removeNode(Node $node): self
    {
        if ($this->nodes->removeElement($node)) {
            // set the owning side to null (unless already changed)
            if ($node->getMenu() === $this) {
                $node->setMenu(null);
            }
        }

        return $this;
    }

    public function getRootNode(): ?Node
    {
        return $this->rootNode;
    }

    public function setRootNode(?Node $rootNode): self
    {
        $this->rootNode = $rootNode;

        return $this;
    }

    public function getRouteName(): string
    {
        return $this->getNavigation()->getRouteName().'_'.($this->getCode() ? $this->getCode() : $this->getId());
    }
}
