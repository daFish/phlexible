<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Component\MetaSet\Model\MetaSetFieldInterface;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Meta set.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSet implements MetaSetInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var int
     * @Assert\Type(type="int")
     */
    private $revision;

    /**
     * @var MetaSetFieldInterface[]|ArrayCollection
     */
    private $fields;

    /**
     * @var string
     */
    private $createdBy;

    /**
     * @var \DateTime
     * @Assert\DateTime
     */
    private $createdAt;

    /**
     * @var string
     */
    private $modifiedBy;

    /**
     * @var \DateTime
     * @Assert\DateTime
     */
    private $modifiedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::generate();
        $this->revision = 1;
        $this->fields = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->modifiedAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * {@inheritdoc}
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function addField(MetaSetFieldInterface $field)
    {
        $this->fields->add($field);
        $field->setMetaSet($this);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasField($name)
    {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getField($name)
    {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return $field;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldById($id)
    {
        foreach ($this->fields as $field) {
            if ((string) $field->getId() === (string) $id) {
                return $field;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function removeField(MetaSetFieldInterface $field)
    {
        if ($this->fields->contains($field)) {
            $this->fields->removeElement($field);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * {@inheritdoc}
     */
    public function setModifiedBy($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }
}
