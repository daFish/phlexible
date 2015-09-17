<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ProblemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Problem.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="problem")
 */
class Problem
{
    const SEVERITY_CRITICAL = 'critical';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_NOTICE = 'notice';
    const SEVERITY_INFO = 'info';

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $severity;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $message;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hint;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_checked_at", type="datetime")
     */
    private $lastCheckedAt;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    private $attributes = array();

    /**
     * @var bool
     */
    private $isLive = false;

    /**
     * @param string $id
     * @param string $severity
     * @param string $message
     * @param string $hint
     * @param array  $attributes
     */
    public function __construct($id, $severity, $message, $hint = null, array $attributes = array())
    {
        $this->id = $id;
        $this->severity = $severity;
        $this->message = $message;
        $this->hint = $hint;
        $this->attributes = $attributes;

        $this->createdAt = new \DateTime();
        $this->lastCheckedAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes = null)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function getAttribute($key, $defaultValue = null)
    {
        if (!$this->hasAttribute($key)) {
            return $defaultValue;
        }

        return $this->attributes[$key];
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function removeAttribute($key)
    {
        unset($this->attributes[$key]);

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
     * @return \DateTime
     */
    public function getLastCheckedAt()
    {
        return $this->lastCheckedAt;
    }

    /**
     * @param bool $isLive
     *
     * @return $this
     */
    public function setLive($isLive = true)
    {
        $this->isLive = $isLive;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLive()
    {
        return $this->isLive;
    }
}
