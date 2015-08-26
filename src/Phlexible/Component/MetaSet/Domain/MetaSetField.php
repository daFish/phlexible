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

use Phlexible\Component\MetaSet\Model\MetaSetFieldInterface;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Meta set field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetField implements MetaSetFieldInterface
{
    /**
     * @var int
     * @Assert\NotBlank
     * @Assert\Uuid
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank
     */
    private $type;

    /**
     * @var string
     */
    private $options;

    /**
     * @var bool
     * @Assert\Type(type="bool")
     */
    private $synchronized = false;

    /**
     * @var bool
     * @Assert\Type(type="bool")
     */
    private $readonly = false;

    /**
     * @var bool
     * @Assert\Type(type="bool")
     */
    private $required = false;

    /**
     * @var MetaSet
     */
    private $metaSet;

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
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequired($required = true)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadonly()
    {
        return $this->readonly;
    }

    /**
     * {@inheritdoc}
     */
    public function setReadonly($readonly = true)
    {
        $this->readonly = $readonly;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSynchronized()
    {
        return $this->synchronized;
    }

    /**
     * {@inheritdoc}
     */
    public function setSynchronized($synchronized = true)
    {
        $this->synchronized = $synchronized;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaSet()
    {
        return $this->metaSet;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaSet(MetaSetInterface $metaSet = null)
    {
        $this->metaSet = $metaSet;

        return $this;
    }
}
