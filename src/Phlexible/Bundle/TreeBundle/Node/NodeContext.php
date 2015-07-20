<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node;

use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\NodeInterface;
use Phlexible\Component\AccessControl\Model\DomainObjectInterface;

/**
 * Node context
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeContext implements DomainObjectInterface
{
    /**
     * @var NodeInterface
     */
    protected $node;

    /**
     * @var TreeInterface
     */
    protected $tree;

    /**
     * @var string
     */
    protected $language;

    /**
     * @param NodeInterface $node
     * @param TreeInterface $tree
     * @param string        $language
     */
    public function __construct(NodeInterface $node, TreeInterface $tree, $language)
    {
        $this->node = $node;
        $this->tree = $tree;
        $this->language = $language;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectIdentifier()
    {
        return $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectType()
    {
        return get_class($this);
    }

    /**
     * @return NodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return TreeInterface
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->node->getId();
    }

    /**
     * @return int
     */
    public function getSiterootId()
    {
        return $this->node->getSiterootId();
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->node->getContentType();
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->node->getSort();
    }

    /**
     * @return string
     */
    public function getSortMode()
    {
        return $this->node->getSortMode();
    }

    /**
     * @return string
     */
    public function getSortDir()
    {
        return $this->node->getSortDir();
    }

    /**
     * @return string
     */
    public function getContentId()
    {
        return $this->node->getContentId();
    }

    /**
     * @param string $language
     *
     * @return bool
     */
    public function getTitle($language = null)
    {
        return $this->getField("page", $language ?: $this->language);
    }

    /**
     * @param NodeContext $nodeContext
     *
     * @return bool
     */
    public function isChildOf(NodeContext $nodeContext)
    {
        return $this->tree->isChildOf($this, $nodeContext);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->node->getAttributes();
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return array
     */
    public function getAttribute($key, $default = null)
    {
        return $this->node->getAttribute($key, $default);
    }

    /**
     * @return bool
     */
    public function getInNavigation()
    {
        return $this->node->getInNavigation();
    }

    /**
     * @return string
     */
    public function getCreateUserId()
    {
        return $this->node->getCreateUserId();
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->node->getCreatedAt($this);
    }

    /**
     * @param string $language
     *
     * @return bool
     */
    public function isPublished($language = null)
    {
        return $this->tree->isPublished($this, $language ?: $this->language);
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getPublishUserId($language = null)
    {
        return $this->tree->getPublishUserId($this, $language ?: $this->language);
    }

    /**
     * @param string $language
     *
     * @return \DateTime|null
     */
    public function getPublishedAt($language = null)
    {
        return $this->tree->getPublishedAt($this, $language ?: $this->language);
    }

    /**
     * @param string $language
     *
     * @return int
     */
    public function getPublishedVersion($language = null)
    {
        return $this->tree->getPublishedVersion($this, $language ?: $this->language);
    }

    /**
     * @param string $language
     *
     * @return bool
     */
    public function isAsync($language = null)
    {
        return $this->tree->isAsync($this, $language ?: $this->language);
    }

    /**
     * @return NodeContext[]
     */
    public function getSiblings()
    {
        return array_merge($this->getBefore(), $this->getAfter());
    }

    /**
     * @return NodeContext[]
     */
    public function getBefore()
    {
        $parentNode = $this->getParent();

        $children = array();
        $keep = true;
        foreach ($this->tree->getChildren($parentNode) as $childNode) {
            if ($childNode->getId() === $this->node->getId()) {
                $keep = false;
                continue;
            }
            if ($keep && $childNode->isAvailable()) {
                $children[] = $childNode;
            }
        }

        return $children;
    }

    /**
     * @return NodeContext[]
     */
    public function getAfter()
    {
        $parentNode = $this->getParent();

        $children = array();
        $keep = false;
        foreach ($this->tree->getChildren($parentNode) as $childNode) {
            if ($childNode->getId() === $this->node->getId()) {
                $keep = true;
                continue;
            }
            if ($keep && $childNode->isAvailable()) {
                $children[] = $childNode;
            }
        }

        return $children;
    }

    /**
     * @return NodeContext|null
     */
    public function getPrevious()
    {
        $before = $this->getBefore();
        if (!count($before)) {
            return null;
        }

        return end($before);
    }

    /**
     * @return NodeContext|null
     */
    public function getNext()
    {
        $after = $this->getAfter();
        if (!count($after)) {
            return null;
        }

        return current($after);
    }

    /**
     * @return NodeContext|null
     */
    public function getParent()
    {
        return $this->tree->getParent($this);
    }

    /**
     * @return NodeContext[]
     */
    public function getChildren()
    {
        $children = array();
        foreach ($this->tree->getChildren($this) as $childNode) {
            if ($childNode->isAvailable()) {
                $children[] = $childNode;
            }
        }

        return $children;
    }

    /**
     * @return NodeContext[]
     */
    public function getPath()
    {
        $path = array();
        $current = $this;
        do {
            $path[] = $current;
        } while ($current = $current->getParent());

        return array_reverse($path);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return true;
    }

    /**
     * @param string $language
     *
     * @return bool
     */
    public function isAvailable($language = null)
    {
        return $this->isPublished($this, $language ?: $this->language);
    }

    /**
     * @return bool
     */
    public function isViewable()
    {
        return $this->tree->isViewable($this);
    }

    /**
     * @return bool
     */
    public function hasViewableChildren()
    {
        return $this->tree->hasViewableChildren($this);
    }

    /**
     * @param string $language
     * @param int    $version
     *
     * @return mixed
     */
    public function getContent($language = null, $version = null)
    {
        return $this->tree->getContent($this, $language ?: $this->language, $version);
    }

    /**
     * @return array
     */
    public function getContentVersions()
    {
        return $this->tree->getContentVersions($this);
    }

    /**
     * @return mixed
     */
    public function getFieldMappings()
    {
        return $this->tree->getFieldMappings($this);
    }

    /**
     * @param string $field
     * @param string $language
     *
     * @return mixed
     */
    public function getField($field, $language = null)
    {
        return $this->tree->getField($this, $field, $language ?: $this->language);
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->tree->getTemplate($this);
    }
}
