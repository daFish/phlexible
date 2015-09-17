<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Lock;

use InvalidArgumentException;

/**
 * Lock identifier.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LockIdentifier implements LockIdentityInterface
{
    /**
     * @var array
     */
    private $delimiter = '__';

    /**
     * @var array
     */
    private $args = array();

    /**
     * Create a new identifier based on the given parameters.
     *
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        $args = func_get_args();

        if (!count($args) || !implode('', $args)) {
            throw new InvalidArgumentException('No identifiers received');
        }

        array_unshift($args, str_replace('\\', '-', get_class($this)));

        $this->args = $args;
    }

    /**
     * Return dtring representation of this identifier.
     *
     * @return string
     */
    public function __toString()
    {
        return str_replace('-', '_', implode($this->delimiter, $this->args));
    }
}
