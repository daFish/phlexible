<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Elementtype\Domain;

use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Elementtype.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Elementtype
{
    const TYPE_FULL = 'full';
    const TYPE_STRUCTURE = 'structure';
    const TYPE_LAYOUTAREA = 'layout';
    const TYPE_LAYOUTCONTAINER = 'layoutcontainer';
    const TYPE_PART = 'part';
    const TYPE_REFERENCE = 'reference';

    /**
     * @var string
     * @Assert\NotNull
     */
    private $id;

    /**
     * @var string
     * @Assert\NotNull
     * @Type("string")
     */
    private $name;

    /**
     * @var int
     * @Assert\NotNull
     * @Type("integer")
     */
    private $revision = 1;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\Choice(choices={"full", "part", "reference", "layout", "structure"})
     * @Type("string")
     */
    private $type;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var string
     */
    private $defaultTab = 'data';

    /**
     * @var bool
     */
    private $hideChildren = false;

    /**
     * @var bool
     */
    private $noIndex = false;

    /**
     * @var bool
     */
    private $deleted = false;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var int
     */
    private $defaultContentTab;

    /**
     * @var string
     */
    private $metaSetId;

    /**
     * @var string
     */
    private $template;

    /**
     * @var array
     */
    private $mappings = array();

    /**
     * @var ElementtypeStructure
     * @Exclude)
     */
    private $structure;

    /**
     * @var \DateTime
     * @Assert\NotNull()
     * @Type("DateTime")
     */
    private $createdAt;

    /**
     * @var string
     * @Assert\NotNull()
     * @Type("string")
     */
    private $createUser;

    /**
     * @var \DateTime
     * @Assert\NotNull()
     * @Type("DateTime")
     */
    private $modifiedAt;

    /**
     * @var string
     * @Assert\NotNull()
     * @Type("string")
     */
    private $modifyUser;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->modifiedAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * @param int $revision
     *
     * @return $this
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Return element type icon.
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Return element type default tab.
     *
     * @return string
     */
    public function getDefaultTab()
    {
        return $this->defaultTab;
    }

    /**
     * @param int $defaultTab
     *
     * @return $this
     */
    public function setDefaultTab($defaultTab)
    {
        $this->defaultTab = $defaultTab ?: null;

        return $this;
    }

    /**
     * @return bool
     */
    public function getHideChildren()
    {
        return $this->hideChildren;
    }

    /**
     * @param bool $hideChildren
     *
     * @return $this
     */
    public function setHideChildren($hideChildren)
    {
        $this->hideChildren = (bool) $hideChildren;

        return $this;
    }

    /**
     * @return bool
     */
    public function getNoIndex()
    {
        return $this->noIndex;
    }

    /**
     * @param bool $noIndex
     *
     * @return $this
     */
    public function setNoIndex($noIndex)
    {
        $this->noIndex = (bool) $noIndex;

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
     * @return string
     */
    public function getCreateUser()
    {
        return $this->createUser;
    }

    /**
     * @param string $createUser
     *
     * @return $this
     */
    public function setCreateUser($createUser)
    {
        $this->createUser = $createUser;

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
     * @return string
     */
    public function getModifyUser()
    {
        return $this->modifyUser;
    }

    /**
     * @param string $modifyUser
     *
     * @return $this
     */
    public function setModifyUser($modifyUser)
    {
        $this->modifyUser = $modifyUser;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     *
     * @return $this
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return ElementtypeStructure
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @param ElementtypeStructure $structure
     *
     * @return $this
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * Return element type default content tab.
     *
     * @return string
     */
    public function getDefaultContentTab()
    {
        return $this->defaultContentTab;
    }

    /**
     * @param int $defaultContentTab
     *
     * @return $this
     */
    public function setDefaultContentTab($defaultContentTab)
    {
        $this->defaultContentTab = $defaultContentTab !== null ? (int) $defaultContentTab : null;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaSetId()
    {
        return $this->metaSetId;
    }

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function setMetaSetId($metaSetId)
    {
        $this->metaSetId = $metaSetId;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return array
     */
    public function getMappings()
    {
        return $this->mappings;
    }

    /**
     * @param array $mappings
     *
     * @return $this
     */
    public function setMappings(array $mappings = null)
    {
        $this->mappings = $mappings;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }
}
