<?php

namespace App\Core\Entity\Site;

use App\Core\Doctrine\Timestampable;
use App\Core\Entity\EntityInterface;
use App\Core\Entity\NavigationSetting;
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
     * @ORM\Column(type="string", length=255)
     */
    protected $domain;

    /**
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    protected $forceDomain = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $additionalDomains = '[]';

    /**
     * @ORM\OneToMany(targetEntity=Menu::class, mappedBy="navigation")
     */
    protected $menus;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $locale = 'en';

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sortOrder;

    /**
     * @ORM\OneToMany(targetEntity=NavigationSetting::class, mappedBy="navigation", orphanRemoval=true)
     */
    protected $navigationSettings;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
        $this->navigationSettings = new ArrayCollection();
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

    public function getForceDomain(): ?bool
    {
        return $this->forceDomain;
    }

    public function setForceDomain(bool $forceDomain): self
    {
        $this->forceDomain = $forceDomain;

        return $this;
    }

    public function getAdditionalDomains(): array
    {
        return (array) json_decode($this->additionalDomains, true);
    }

    public function setAdditionalDomains(array $additionalDomains): self
    {
        $this->additionalDomains = json_encode($additionalDomains);

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

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * @return Collection|NavigationSetting[]
     */
    public function getNavigationSettings(): Collection
    {
        return $this->navigationSettings;
    }

    public function addNavigationSetting(NavigationSetting $navigationSetting): self
    {
        if (!$this->navigationSettings->contains($navigationSetting)) {
            $this->navigationSettings[] = $navigationSetting;
            $navigationSetting->setNavigation($this);
        }

        return $this;
    }

    public function removeNavigationSetting(NavigationSetting $navigationSetting): self
    {
        if ($this->navigationSettings->removeElement($navigationSetting)) {
            // set the owning side to null (unless already changed)
            if ($navigationSetting->getNavigation() === $this) {
                $navigationSetting->setNavigation(null);
            }
        }

        return $this;
    }
}
