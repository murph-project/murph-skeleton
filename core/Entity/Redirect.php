<?php

namespace App\Core\Entity;

use App\Core\Repository\RedirectRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RedirectRepository::class)
 */
class Redirect implements EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=5)
     */
    protected $scheme;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $domain;

    /**
     * @ORM\Column(type="string", length=6)
     */
    protected $domainType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $rule;

    /**
     * @ORM\Column(type="string", length=6)
     */
    protected $ruleType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $location;

    /**
     * @ORM\Column(type="integer")
     */
    protected $redirectCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $label;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sortOrder;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isEnabled;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $reuseQueryString;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    public function setScheme(string $scheme): self
    {
        $this->scheme = $scheme;

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

    public function getDomainType(): ?string
    {
        return $this->domainType;
    }

    public function setDomainType(string $domainType): self
    {
        $this->domainType = $domainType;

        return $this;
    }

    public function getRule(): ?string
    {
        return $this->rule;
    }

    public function setRule(string $rule): self
    {
        $this->rule = $rule;

        return $this;
    }

    public function getRuleType(): ?string
    {
        return $this->ruleType;
    }

    public function setRuleType(string $ruleType): self
    {
        $this->ruleType = $ruleType;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getRedirectCode(): ?int
    {
        return $this->redirectCode;
    }

    public function setRedirectCode(int $redirectCode): self
    {
        $this->redirectCode = $redirectCode;

        return $this;
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

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    public function getIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getReuseQueryString(): ?bool
    {
        return $this->reuseQueryString;
    }

    public function setReuseQueryString(bool $reuseQueryString): self
    {
        $this->reuseQueryString = $reuseQueryString;

        return $this;
    }
}
