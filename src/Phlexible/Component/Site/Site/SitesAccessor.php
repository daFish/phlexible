<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Site\Site;

use Phlexible\Component\Site\Model\SiteManagerInterface;

/**
 * Siteroots accessor.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SitesAccessor implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var SiteManagerInterface
     */
    private $siteManager;

    /**
     * @param SiteManagerInterface $siteManager
     */
    public function __construct(SiteManagerInterface $siteManager)
    {
        $this->siteManager = $siteManager;
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset
     *
     * @return bool true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return $this->siteManager->find($offset) !== null;
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->siteManager->find($offset);
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->siteManager->findAll());
    }
}
