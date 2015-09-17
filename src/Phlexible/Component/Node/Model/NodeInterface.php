<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Node\Model;

/**
 * Tree node interface.
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
     * @return string
     */
    public function getWorkspace();

    /**
     * @param string $workspace
     *
     * @return $this
     */
    public function setWorkspace($workspace);

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
    public function getPath();

    /**
     * @return string
     */
    public function getParentPath();

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path);

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getNavigationTitle();

    /**
     * @param string $navigationTitle
     *
     * @return $this
     */
    public function setNavigationTitle($navigationTitle);

    /**
     * @return string
     */
    public function getBackendTitle();

    /**
     * @param string $backendTitle
     *
     * @return $this
     */
    public function setBackendTitle($backendTitle);

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug);

    /**
     * @return string
     */
    public function getContentType();

    /**
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentType($contentType);

    /**
     * @return string
     */
    public function getContentId();

    /**
     * @param string $contentId
     *
     * @return $this
     */
    public function setContentId($contentId);

    /**
     * @return string
     */
    public function getContentVersion();

    /**
     * @param int $contentVersion
     *
     * @return $this
     */
    public function setContentVersion($contentVersion);

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
