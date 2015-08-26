<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Node\Domain;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Component\Node\Model\NodeInterface;

/**
 * Node
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class Node implements NodeInterface
{
    /**
     * @var int
     */
    private $persistanceId;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $workspace;

    /**
     * @var int
     */
    private $parentId;

    /**
     * @var string
     */
    private $siterootId;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $parentPath;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $navigationTitle;

    /**
     * @var string
     */
    private $backendTitle;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var int
     */
    private $contentId;

    /**
     * @var int
     */
    private $contentVersion;

    /**
     * @var int
     */
    private $sort = 0;

    /**
     * @var string
     */
    private $sortMode = 'free';

    /**
     * @var string
     */
    private $sortDir = 'asc';

    /**
     * @var string
     */
    private $attributes;

    /**
     * @var string
     */
    private $createUserId;

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @var string
     */
    private $modifyUserId;

    /**
     * @var DateTime
     */
    private $modifiedAt;

    /**
     * @var string
     */
    private $publishUserId;

    /**
     * @var DateTime
     */
    private $publishedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->mappedFields = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot()
    {
        return $this->getParentId() === null;
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
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * {@inheritdoc}
     */
    public function setWorkspace($workspace)
    {
        $this->workspace = $workspace;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * {@inheritdoc}
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSiterootId()
    {
        return $this->siterootId;
    }

    /**
     * {@inheritdoc}
     */
    public function setSiterootId($siterootId)
    {
        $this->siterootId = $siterootId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNavigationTitle()
    {
        return $this->navigationTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setNavigationTitle($navigationTitle)
    {
        $this->navigationTitle = $navigationTitle;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBackendTitle()
    {
        return $this->backendTitle;
    }

    /**
     * {@inheritdoc}
     */
    public function setBackendTitle($backendTitle)
    {
        $this->backendTitle = $backendTitle;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * {@inheritdoc}
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentVersion()
    {
        return $this->contentVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setContentVersion($contentVersion)
    {
        $this->contentVersion = $contentVersion;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * {@inheritdoc}
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortMode()
    {
        return $this->sortMode;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortMode($sortMode)
    {
        $this->sortMode = $sortMode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortDir()
    {
        return $this->sortDir;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortDir($sortDir)
    {
        $this->sortDir = $sortDir;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attributes = null)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($key, $default = null)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttribute($key)
    {
        if (isset($this->attributes[$key])) {
            unset($this->attributes[$key]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateUserId()
    {
        return $this->createUserId;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreateUserId($createUserId)
    {
        $this->createUserId = $createUserId;

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
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return int
     */
    public function getPersistanceId()
    {
        return $this->persistanceId;
    }

    /**
     * @return string
     */
    public function getModifyUserId()
    {
        return $this->modifyUserId;
    }

    /**
     * @param string $modifyUserId
     *
     * @return $this
     */
    public function setModifyUserId($modifyUserId)
    {
        $this->modifyUserId = $modifyUserId;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param DateTime $modifiedAt
     *
     * @return $this
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublishUserId()
    {
        return $this->publishUserId;
    }

    /**
     * @param string $publishUserId
     *
     * @return $this
     */
    public function setPublishUserId($publishUserId)
    {
        $this->publishUserId = $publishUserId;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTime $publishedAt
     *
     * @return $this
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getParentPath()
    {
        return $this->parentPath;
    }

    /**
     * @param string $parentPath
     *
     * @return $this
     */
    public function setParentPath($parentPath)
    {
        $this->parentPath = $parentPath;

        return $this;
    }
}
