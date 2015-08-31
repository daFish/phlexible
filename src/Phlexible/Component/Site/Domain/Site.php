<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Site\Domain;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;
use Phlexible\Component\NodeType\Domain\NodeTypeConstraint;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Site
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Serializer\XmlRoot(name="site")
 * @Serializer\ExclusionPolicy("all")
 */
class Site
{
    /**
     * @var string
     * @Serializer\Type(name="string")
     * @Serializer\Expose()
     * @Serializer\XmlAttribute()
     */
    private $id;

    /**
     * @var bool
     * @Serializer\Expose()
     * @Serializer\Type(name="boolean")
     * @Serializer\XmlAttribute()
     */
    private $default = false;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Serializer\Expose()
     * @Serializer\Type(name="string")
     * @Serializer\XmlAttribute()
     */
    private $hostname;

    /**
     * @var DateTime
     * @Assert\NotBlank()
     * @Serializer\Expose()
     * @Serializer\Type(name="DateTime")
     * @Serializer\XmlAttribute()
     */
    private $createdAt;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Serializer\Expose()
     * @Serializer\Type(name="string")
     * @Serializer\XmlAttribute()
     */
    private $createdBy;

    /**
     * @var DateTime
     * @Assert\NotBlank()
     * @Serializer\Expose()
     * @Serializer\Type(name="DateTime")
     * @Serializer\XmlAttribute()
     */
    private $modifiedAt;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Serializer\Expose()
     * @Serializer\Type(name="string")
     * @Serializer\XmlAttribute()
     */
    private $modifiedBy;

    /**
     * @var array
     * @Serializer\Expose()
     * @Serializer\Type(name="array<string, string>")
     * @Serializer\XmlMap(inline=false, entry="title", keyAttribute="language")
     */
    private $titles = array();

    /**
     * @var array
     * @Serializer\Expose()
     * @Serializer\Type(name="array<string, string>")
     * @Serializer\XmlMap(inline=false, entry="property", keyAttribute="key")
     */
    private $properties = array();

    /**
     * @var NodeAlias[]|ArrayCollection
     * @Serializer\Expose()
     * @Serializer\Type(name="ArrayCollection<Phlexible\Component\Site\Domain\NodeAlias>")
     * @Serializer\XmlList(inline=false, entry="nodeAlias")
     */
    private $nodeAliases;

    /**
     * @var Navigation[]|ArrayCollection
     * @Assert\Valid
     * @Serializer\Expose()
     * @Serializer\Type(name="ArrayCollection<Phlexible\Component\Site\Domain\Navigation>")
     * @Serializer\XmlList(inline=false, entry="navigation")
     */
    private $navigations;

    /**
     * @var EntryPoint[]|ArrayCollection
     * @Assert\Valid
     * @Serializer\Expose()
     * @Serializer\Type(name="ArrayCollection<Phlexible\Component\Site\Domain\EntryPoint>")
     * @Serializer\XmlList(inline=false, entry="entryPoint")
     */
    private $entryPoints;

    /**
     * @var NodeTypeConstraint[]|ArrayCollection
     * @Assert\Valid
     * @Serializer\Expose()
     * @Serializer\Type(name="ArrayCollection<Phlexible\Component\NodeType\Domain\NodeTypeConstraint>")
     * @Serializer\XmlList(inline=false, entry="nodeConstraint")
     */
    private $nodeConstraints;

    /**
     * Constructor.
     *
     * @param string $id
     */
    public function __construct($id = null)
    {
        if (null !== $id) {
            $this->id = $id;
        }

        $this->createdAt = new DateTime();
        $this->modifiedAt = new DateTime();
        $this->navigations = new ArrayCollection();
        $this->nodeAliases = new ArrayCollection();
        $this->entryPoints = new ArrayCollection();
        $this->nodeConstraints = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param bool $default
     *
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = (bool) $default;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param string $hostname
     *
     * @return $this
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * @param string $createdBy
     *
     * @return $this
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $modifiedBy
     *
     * @return $this
     */
    public function setModifiedBy($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * @return string
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * @param DateTime $modifiedAt
     *
     * @return $this
     */
    public function setModifiedAt(DateTime $modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set all titles
     *
     * @param array $titles
     *
     * @return $this
     */
    public function setTitles(array $titles)
    {
        $this->titles = $titles;

        return $this;
    }

    /**
     * Return all titles
     *
     * @return array
     */
    public function getTitles()
    {
        return $this->titles;
    }

    /**
     * Set title
     *
     * @param string $language
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($language, $title)
    {
        $this->titles[$language] = $title;

        return $this;
    }

    /**
     * Return siteroot title
     *
     * @param string $language
     *
     * @return string
     */
    public function getTitle($language = null)
    {
        $fallbackLanguage = key($this->titles);
        if ($language === null) {
            $language = $fallbackLanguage;
        }

        if (!empty($this->titles[$language])) {
            return $this->titles[$language];
        }

        if (!empty($this->titles[$fallbackLanguage])) {
            return $this->titles[$fallbackLanguage];
        }

        $title = $this->getHostname();

        if (!$title) {
            return '(No title)';
        }

        return $title;
    }

    /**
     * @param Navigation[] $navigations
     *
     * @return $this
     */
    public function setNavigations(array $navigations)
    {
        foreach ($navigations as $navigation) {
            $this->setNavigation($navigation);
        }

        return $this;
    }

    /**
     * @return Navigation[]
     */
    public function getNavigations()
    {
        return $this->navigations->getValues();
    }

    /**
     * @param Navigation $navigation
     *
     * @return $this
     */
    public function setNavigation(Navigation $navigation)
    {
        if (!$this->navigations->contains($navigation)) {
            $this->navigations->add($navigation);
        }

        return $this;
    }

    /**
     * @param Navigation $navigation
     *
     * @return $this
     */
    public function removeNavigation(Navigation $navigation)
    {
        if ($this->navigations->contains($navigation)) {
            $this->navigations->removeElement($navigation);
        }

        return $this;
    }

    /**
     * @param NodeAlias[] $nodeAliases
     *
     * @return $this
     */
    public function setNodeAliases(array $nodeAliases)
    {
        foreach ($nodeAliases as $nodeAlias) {
            $this->setNodeAlias($nodeAlias);
        }

        return $this;
    }

    /**
     * @return NodeAlias[]
     */
    public function getNodeAliases()
    {
        return $this->nodeAliases->getValues();
    }

    /**
     * @param NodeAlias $nodeAlias
     *
     * @return $this
     */
    public function setNodeAlias(NodeAlias $nodeAlias)
    {
        if (!$this->nodeAliases->contains($nodeAlias)) {
            $this->nodeAliases->add($nodeAlias);
        }

        return $this;
    }

    /**
     * @param NodeAlias $nodeAlias
     *
     * @return $this
     */
    public function removeNodeAlias(NodeAlias $nodeAlias)
    {
        $this->nodeAliases[] = $nodeAlias;

        return $this;
    }

    /**
     * @param string $name
     * @param string $language
     *
     * @return NodeAlias
     */
    public function getNodeAlias($name, $language = null)
    {
        $candidates = array();

        foreach ($this->nodeAliases as $nodeAlias) {
            if ($nodeAlias->getName() === $name) {
                $candidates[$nodeAlias->getLanguage()] = $nodeAlias;
            }
        }

        if (isset($candidates[$language])) {
            return $candidates[$language];
        }

        if (isset($candidates[null])) {
            return $candidates[null];
        }

        return null;
    }

    /**
     * Return all special tids
     *
     * @return NodeAlias[]
     * @deprecated
     */
    public function getSpecialTids()
    {
        return $this->getNodeAliases();
    }

    /**
     * Return a special tid
     *
     * @param string $language
     * @param string $key
     *
     * @return string
     * @deprecated
     */
    public function getSpecialTid($language, $key)
    {
        return $this->getNodeAlias($key, $language);
    }

    /**
     * @param EntryPoint[] $entryPoints
     *
     * @return $this
     */
    public function setEntryPoints(array $entryPoints)
    {
        foreach ($entryPoints as $entryPoint) {
            $this->setEntryPoint($entryPoint);
        }

        return $this;
    }

    /**
     * @return EntryPoint[]
     */
    public function getEntryPoints()
    {
        return $this->entryPoints->getValues();
    }

    /**
     * @param EntryPoint $entryPoint
     *
     * @return $this
     */
    public function setEntryPoint(EntryPoint $entryPoint)
    {
        if (!$this->entryPoints->contains($entryPoint)) {
            $this->entryPoints->add($entryPoint);
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function removeEntryPoint($name)
    {
        if ($this->entryPoints->contains($name)) {
            $this->entryPoints->removeElement($name);
        }

        return $this;
    }

    /**
     * @param array $properties
     *
     * @return $this
     */
    public function setProperties(array $properties)
    {
        foreach ($properties as $key => $value) {
            $this->setProperty($key, $value);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        $properties = $this->properties;

        return $properties;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setProperty($key, $value)
    {
        $this->properties[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getProperty($key)
    {
        if (empty($this->properties[$key])) {
            return null;
        }

        return $this->properties[$key];
    }

    /**
     * @return NodeTypeConstraint[]
     */
    public function getNodeConstraints()
    {
        return $this->nodeConstraints->getValues();
    }

    /**
     * @param NodeTypeConstraint[] $nodeConstraints
     *
     * @return $this
     */
    public function setNodeConstraints(array $nodeConstraints)
    {
        foreach ($nodeConstraints as $nodeConstraint) {
            $this->setNodeConstraint($nodeConstraint);
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return NodeTypeConstraint
     */
    public function getNodeConstraint($name)
    {
        if ($this->nodeConstraints->containsKey($name)) {
            return $this->nodeConstraints->get($name);
        }

        return null;
    }

    /**
     * @param NodeTypeConstraint $nodeConstraint
     *
     * @return $this
     */
    public function setNodeConstraint(NodeTypeConstraint $nodeConstraint)
    {
        if (!$this->nodeConstraints->containsKey($nodeConstraint->getName())) {
            $this->nodeConstraints->set($nodeConstraint->getName(), $nodeConstraint);
        }

        return $this;
    }
}
