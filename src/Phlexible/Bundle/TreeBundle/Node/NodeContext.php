<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Node;

use DateTime;
use Phlexible\Bundle\TreeBundle\Mediator\MediatorInterface;
use Phlexible\Bundle\TreeBundle\Model\PageInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Component\AccessControl\Model\DomainObjectInterface;
use Phlexible\Component\Node\Model\NodeInterface;

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
     * @var MediatorInterface
     */
    protected $mediator;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var mixed
     */
    private $content;

    /**
     * @param NodeInterface     $node
     * @param TreeInterface     $tree
     * @param MediatorInterface $mediator
     * @param string            $language
     */
    public function __construct(NodeInterface $node, TreeInterface $tree, MediatorInterface$mediator, $language)
    {
        $this->node = $node;
        $this->tree = $tree;
        $this->mediator = $mediator;
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
     * @return MediatorInterface
     */
    public function getMediator()
    {
        return $this->mediator;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->node->getId();
    }

    /**
     * @return string
     */
    public function getWorkspace()
    {
        return $this->node->getWorkspace();
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
    public function getPath()
    {
        return $this->node->getPath();
    }

    /**
     * @return string
     */
    public function getParentPath()
    {
        return $this->node->getParentPath();
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->node->getLocale();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->node->getTitle();
    }

    /**
     * @return string
     */
    public function getNavigationTitle()
    {
        return $this->node->getNavigationTitle();
    }

    /**
     * @return string
     */
    public function getBackendTitle()
    {
        return $this->node->getBackendTitle();
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->node->getSlug();
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->node->getContentType();
    }

    /**
     * @return string
     */
    public function getContentId()
    {
        return $this->node->getContentId();
    }

    /**
     * @return string
     */
    public function getContentVersion()
    {
        return $this->node->getContentVersion();
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
     * @return string
     */
    public function getCreateUserId()
    {
        return $this->node->getCreateUserId();
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->node->getCreatedAt();
    }

    /**
     * @return string|null
     */
    public function getModifyUserId()
    {
        return $this->node->getModifyUserId();
    }

    /**
     * @return DateTime|null
     */
    public function getModifiedAt()
    {
        return $this->node->getModifiedAt();
    }

    /**
     * @return string|null
     */
    public function getPublishUserId()
    {
        return $this->node->getPublishUserId();
    }

    /**
     * @return DateTime|null
     */
    public function getPublishedAt()
    {
        return $this->node->getPublishedAt();
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
     * @return bool
     */
    public function getInNavigation()
    {
        return ($this->node instanceof PageInterface) ? $this->node->getInNavigation() : false;
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
    }

    /**
     * @return bool
     */
    public function isAsync()
    {
        return $this->tree->isAsync($this);
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
    public function getNodePath()
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
        return $this->isPublished($language ?: $this->language);
    }

    /**
     * @param string $language
     *
     * @return bool
     */
    public function isViewable($language = null)
    {
        return $this->node instanceof PageInterface &&
            $this->isPublished($language ?: $this->language) &&
            $this->getNode()->getInNavigation();
    }

    /**
     * @param string $language
     *
     * @return bool
     */
    public function hasViewableChildren($language = null)
    {
        foreach ($this->getChildren() as $childNode) {
            if ($childNode->isViewable($language ?: $this->language)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $language
     * @param int    $version
     *
     * @return mixed
     */
    public function getContent($language = null, $version = null)
    {
        if (null === $this->content) {
            $this->content = $this->mediator->getContent($this, $language ?: $this->language, $version);
        }

        return $this->content;
    }

    /**
     * @return array
     */
    public function getContentVersions()
    {
        return $this->mediator->getContentVersions($this);
    }

    /**
     * @return mixed
     */
    public function getFieldMappings()
    {
        return $this->mediator->getFieldMappings($this);
    }

    /**
     * @param string $field
     * @param string $language
     *
     * @return mixed
     */
    public function getField($field, $language = null)
    {
        return $this->mediator->getField($this, $field, $language ?: $this->language);
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        $template = $this->mediator->getTemplate($this);

        if (!$template) {
            $template = $this->node->getTemplate();

            if (!$template) {
                $template = '::' . $this->node->getContentType() . '.html.twig';
            }
        }

        return $template;
    }
}
