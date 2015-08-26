<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\Domain;

use Phlexible\Component\NodeType\Domain\NodeTypeConstraint;

/**
 * Site
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Site
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var bool
     */
    private $default = false;

    /**
     * @var string
     */
    private $hostname;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var string
     */
    private $createUserId;

    /**
     * @var \DateTime
     */
    private $modifiedAt;

    /**
     * @var  string
     */
    private $modifyUserId;

    /**
     * @var NodeAlias[]
     */
    private $nodeAliases = array();

    /**
     * @var array
     */
    private $titles = array();

    /**
     * @var array
     */
    private $properties = array();

    /**
     * @var Navigation[]
     */
    private $navigations;

    /**
     * @var EntryPoint[]
     */
    private $entryPoints;

    /**
     * @var NodeTypeConstraint[]
     */
    private $nodeConstraints;

    /**
     * Constructor.
     *
     * @param string $uuid
     */
    public function __construct($uuid = null)
    {
        if (null !== $uuid) {
            $this->id = $uuid;
        }
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
     * @param string $createUid
     *
     * @return $this
     */
    public function setCreateUserId($createUid)
    {
        $this->createUserId = $createUid;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreateUserId()
    {
        return $this->createUserId;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $modifyUid
     *
     * @return $this
     */
    public function setModifyUserId($modifyUid)
    {
        $this->modifyUserId = $modifyUid;

        return $this;
    }

    /**
     * @return string
     */
    public function getModifyUserId()
    {
        return $this->modifyUserId;
    }

    /**
     * @param \DateTime $modifiedAt
     *
     * @return $this
     */
    public function setModifiedAt(\DateTime $modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @return \DateTime
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
        return $this->navigations;
    }

    /**
     * @param Navigation $navigation
     *
     * @return $this
     */
    public function setNavigation(Navigation $navigation)
    {
        $this->navigations[$navigation->getName()] = $navigation;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function removeNavigation($name)
    {
        if (isset($this->navigations[$name])) {
            unset($this->navigations[$name]);
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
        return $this->nodeAliases;
    }

    /**
     * @param NodeAlias $nodeAlias
     *
     * @return $this
     */
    public function setNodeAlias(NodeAlias $nodeAlias)
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
        $canidates = array();

        foreach ($this->nodeAliases as $nodeAlias) {
            if ($nodeAlias->getName() === $name) {
                $canidates[$nodeAlias->getLanguage()] = $nodeAlias;
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
        return $this->entryPoints;
    }

    /**
     * @param EntryPoint $entryPoint
     *
     * @return $this
     */
    public function setEntryPoint(EntryPoint $entryPoint)
    {
        $this->entryPoints[$entryPoint->getName()] = $entryPoint;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function removeEntryPoint($name)
    {
        if (isset($this->entryPoints[$name])) {
            unset($this->entryPoints[$name]);
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
        return $this->nodeConstraints;
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
        if (isset($this->nodeConstraints[$name])) {
            return $this->nodeConstraints[$name];
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
        $this->nodeConstraints[$nodeConstraint->getName()] = $nodeConstraint;

        return $this;
    }
}
