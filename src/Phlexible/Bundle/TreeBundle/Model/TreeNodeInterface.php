<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Model;

/**
 * Tree node interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TreeNodeInterface
{
    /**
     * @return TreeInterface
     */
    public function getTree();

    /**
     * @param TreeInterface $tree
     *
     * @return $this
     */
    public function setTree(TreeInterface $tree);

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
     * @return int
     */
    public function getParentId();

    /**
     * @param int $parentId
     *
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * @return bool
     */
    public function isRoot();

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getTypeId();

    /**
     * @param string $typeId
     *
     * @return $this
     */
    public function setTypeId($typeId);

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
    public function getAttribute($key, $default);

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

    /**
     * @return boolean
     */
    public function getInNavigation();

    /**
     * @param boolean $inNavigation
     *
     * @return $this
     */
    public function setInNavigation($inNavigation);

    /**
     * @return array
     */
    public function getCache();

    /**
     * @param array $cache
     *
     * @return $this
     */
    public function setCache($cache);

    /**
     * @return string
     */
    public function getController();

    /**
     * @param string $controller
     *
     * @return $this
     */
    public function setController($controller);

    /**
     * @return array
     */
    public function getRoutes();

    /**
     * @param array $routes
     *
     * @return $this
     */
    public function setRoutes(array $routes);

    /**
     * @return boolean
     */
    public function getNeedsAuthentication();

    /**
     * @param boolean $needsAuthentication
     *
     * @return $this
     */
    public function setNeedsAuthentication($needsAuthentication);
}
