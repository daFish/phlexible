<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\Model;

/**
 * Template interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TemplateInterface
{
    /**
     * @return string
     */
    public function getKey();

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key);

    /**
     * @return string
     */
    public function getType();

    /**
     * @return bool
     */
    public function getCache();

    /**
     * @param bool $cache
     *
     * @return $this
     */
    public function setCache($cache = true);

    /**
     * @return bool
     */
    public function getSystem();

    /**
     * @param bool $system
     *
     * @return $this
     */
    public function setSystem($system = true);

    /**
     * @return string
     */
    public function getStorage();

    /**
     * @param string $storage
     *
     * @return $this
     */
    public function setStorage($storage);

    /**
     * @return int
     */
    public function getRevision();

    /**
     * @param int $revision
     *
     * @return $this
     */
    public function setRevision($revision);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime
     */
    public function getModifiedAt();

    /**
     * @param \DateTime $modifiedAt
     *
     * @return $this
     */
    public function setModifiedAt(\DateTime $modifiedAt);

    /**
     * @return array
     */
    public function toArray();
}
