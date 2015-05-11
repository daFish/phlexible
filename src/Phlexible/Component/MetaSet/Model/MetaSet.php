<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Meta set
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
    private $createUser;

    /**
     * @var \DateTime
     * @Assert\DateTime
     */
    private $createdAt;

    /**
     * @var string
     */
    private $modifyUser;

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
    public function getCreateUser()
    {
        return $this->createUser;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreateUser($createUser)
    {
        $this->createUser = $createUser;

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
    public function getModifyUser()
    {
        return $this->modifyUser;
    }

    /**
     * {@inheritdoc}
     */
    public function setModifyUser($modifyUser)
    {
        $this->modifyUser = $modifyUser;

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
