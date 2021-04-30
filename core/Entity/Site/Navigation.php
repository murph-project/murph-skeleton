<?php

namespace App\Core\Entity\Site;

use App\Core\Doctrine\Timestampable;
use App\Core\Entity\EntityInterface;
use App\Core\Repository\Site\NavigationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NavigationRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Navigation implements EntityInterface
{
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $domain;

    /**
     * @ORM\OneToMany(targetEntity=Menu::class, mappedBy="navigation")
     */
    private $menus;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $locale = 'en';

    public function __construct()
    {
        $this->menus = new ArrayCollection();
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

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return Collection|Menu[]
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): self
    {
        if (!$this->menus->contains($menu)) {
            $this->menus[] = $menu;
            $menu->setNavigation($this);
        }

        return $this;
    }

    public function removeMenu(Menu $menu): self
    {
        if ($this->menus->removeElement($menu)) {
            // set the owning side to null (unless already changed)
            if ($menu->getNavigation() === $this) {
                $menu->setNavigation(null);
            }
        }

        return $this;
    }

    public function getMenu(string $code): ?Menu
    {
        foreach ($this->menus as $menu) {
            if ($menu->getCode() === $code) {
                return $menu;
            }
        }

        return $menu;
    }

    public function getRouteName(): string
    {
        return $this->getCode() ? $this->getCode() : 'navigation_'.$this->getId();
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
