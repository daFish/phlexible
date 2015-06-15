<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\Permission;

use Phlexible\Component\AccessControl\Exception\InvalidArgumentException;

/**
 * Permission
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Permission
{
    /**
     * @var string
     */
    private $contentClass;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $bit;

    /**
     * @var array
     */
    private $iconClass;

    /**
     * @param string $contentClass
     * @param string $name
     * @param int    $bit
     * @param string $iconClass
     *
     * @throws \Phlexible\Component\AccessControl\Exception\InvalidArgumentException
     */
    public function __construct($contentClass, $name, $bit, $iconClass)
    {
        // check for single bit (power of 2), see: http://aggregate.org/MAGIC/#Is%20Power%20of%202
        if ($bit & ($bit - 1)) {
            $bin = decbin($bit);
            throw new InvalidArgumentException("Only a single bit can be set, but got $bin in $contentClass-$securityClass-$name");
        }

        $this->contentClass = $contentClass;
        $this->name = $name;
        $this->bit = $bit;
        $this->iconClass = $iconClass;
    }

    /**
     * @return string
     */
    public function getContentClass()
    {
        return $this->contentClass;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return bit
     *
     * @return int
     */
    public function getBit()
    {
        return $this->bit;
    }

    /**
     * @param int $value
     *
     * @return bool
     */
    public function test($value)
    {
        return $this->bit & (int) $value > 0;
    }

    /**
     * @return string
     */
    public function getIconClass()
    {
        return $this->iconClass;
    }
}
