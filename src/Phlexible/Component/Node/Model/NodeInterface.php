<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Node\Model;

/**
 * Tree node interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface NodeInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @return NodeInterface
     */
    public function getParentNode();

    /**
     * @param NodeInterface $parentNode
     *
     * @return $this
     */
    public function setParentNode($parentNode);

    /**
     * @return bool
     */
    public function isRoot();

    /**
     * @return string
     */
    public function getSiterootId();

    /**
     * @param string $siterootId
     *
     * @return $this
     */
    public function setSiterootId($siterootId);

    /**
     * @return string
     */
    public function getContentType();

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setContentType($type);

    /**
     * @return string
     */
    public function getContentId();

    /**
     * @param string $typeId
     *
     * @return $this
     */
    public function setContentId($typeId);

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes);

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return array
     */
    public function getAttribute($key, $default = null);

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setAttribute($key, $value);

    /**
     * @param string $key
     *
     * @return $this
     */
    public function removeAttribute($key);

    /**
     * @return int
     */
    public function getSort();

    /**
     * @param int $sort
     *
     * @return $this
     */
    public function setSort($sort);

    /**
     * @return string
     */
    public function getSortMode();

    /**
     * @param string $sortMode
     *
     * @return $this
     */
    public function setSortMode($sortMode);

    /**
     * @return string
     */
    public function getSortDir();

    /**
     * @param string $sortDir
     *
     * @return $this
     */
    public function setSortDir($sortDir);

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
     * @return string
     */
    public function getCreateUserId();

    /**
     * @param string $createUid
     *
     * @return $this
     */
    public function setCreateUserId($createUid);
}
