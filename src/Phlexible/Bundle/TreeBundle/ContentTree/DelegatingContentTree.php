<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\TreeBundle\Mediator\TreeMediatorInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeIdentifier;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\NodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;
use Phlexible\Component\Identifier\IdentifiableInterface;

/**
 * Delegating content tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingContentTree implements ContentTreeInterface, \IteratorAggregate, IdentifiableInterface
{
    /**
     * @var TreeInterface
     */
    private $tree;

    /**
     * @var Siteroot
     */
    private $siteroot;

    /**
     * @var TreeMediatorInterface
     */
    private $mediator;

    /**
     * @var string
     */
    private $language;

    /**
     * @param TreeInterface     $tree
     * @param Siteroot          $siteroot
     * @param TreeMediatorInterface $mediator
     * @param string            $language
     */
    public function __construct(TreeInterface $tree, Siteroot $siteroot, TreeMediatorInterface $mediator, $language = null)
    {
        $this->tree = $tree;
        $this->siteroot = $siteroot;
        $this->mediator = $mediator;
        $this->language = $language;
    }

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultLanguage()
    {
        return $this->language;
    }

    /**
     * {@inheritdoc}
     *
     * @return TreeIterator
     */
    public function getIterator()
    {
        return new TreeIterator($this);
    }

    /**
     * {@inheritdoc}
     *
     * @return TreeIdentifier
     */
    public function getIdentifier()
    {
        return new TreeIdentifier($this->getSiterootId());
    }

    /**
     * {@inheritdoc}
     */
    public function getSiterootId()
    {
        return $this->siteroot->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getSiteroot()
    {
        return $this->siteroot;
    }

    /**
     * {@inheritdoc}
     */
    public function isDefaultSiteroot()
    {
        return $this->getSiteroot()->isDefault();
    }

    /**
     * {@inheritdoc}
     */
    public function getUrls()
    {
        return $this->getSiteroot()->getUrls();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultUrl()
    {
        return $this->getSiteroot()->getDefaultUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getNavigations()
    {
        return $this->getSiteroot()->getUrls();
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecialTids($language = null)
    {
        return $this->getSiteroot()->getSpecialTidsForLanguage($language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function createContentTreeNodeFromTreeNode(NodeInterface $treeNode)
    {
        $contentNode = new ContentNode();
        $contentNode
            ->setLanguage($this->language)
            ->setId($treeNode->getId())
            ->setContentId($treeNode->getContentId())
            ->setContentType($treeNode->getContentType())
            ->setTree($this)
            ->setParentNode($treeNode->getParentNode())
            ->setInNavigation($treeNode->getInNavigation())
            ->setSort($treeNode->getSort())
            ->setSortMode($treeNode->getSortMode())
            ->setSortDir($treeNode->getSortDir())
            ->setCreatedAt($treeNode->getCreatedAt())
            ->setCreateUserId($treeNode->getCreateUserId())
            ->setAttributes($treeNode->getAttributes());

        return $contentNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        return $this->tree->getRoot();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $treeNode = $this->tree->get($id);
        if (!$treeNode) {
            return null;
        }

        $contentNode = $this->createContentTreeNodeFromTreeNode($treeNode);

        return $contentNode;
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return $this->tree->has($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren(NodeInterface $node)
    {
        $children = array();
        foreach ($this->tree->getChildren($node) as $childNode) {
            $children[] = $this->get($childNode->getId());
        }

        return $children;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren(NodeInterface $node)
    {
        return $this->tree->hasChildren($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(NodeInterface $node)
    {
        $parentNode = $node->getParentNode();
        if (!$parentNode) {
            return null;
        }

        return $this->get($parentNode->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getIdPath(NodeInterface $node)
    {
        return $this->tree->getIdPath($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(NodeInterface $node)
    {
        $path = array();
        foreach ($this->tree->getPath($node) as $pathNode) {
            $path[] = $this->get($pathNode->getId());
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot(NodeInterface $node)
    {
        return $this->tree->isRoot($node);
    }

    /**
     * {@inheritdoc}
     */
    public function isChildOf(NodeInterface $childNode, NodeInterface $parentNode)
    {
        return $this->tree->isChildOf($childNode, $parentNode);
    }

    /**
     * {@inheritdoc}
     */
    public function isParentOf(NodeInterface $parentNode, NodeInterface $childNode)
    {
        return $this->tree->isChildOf($childNode, $parentNode);
    }

    /**
     * {@inheritdoc}
     */
    public function isInstance(NodeInterface $node)
    {
        return $this->tree->isInstance($node);
    }

    /**
     * {@inheritdoc}
     */
    public function isInstanceMaster(NodeInterface $node)
    {
        return $this->tree->isInstanceMaster($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstances(NodeInterface $node)
    {
        return $this->tree->getInstances($node);
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished(NodeInterface $node, $language = null)
    {
        return $this->tree->isPublished($node, $language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedLanguages(NodeInterface $node)
    {
        return $this->tree->getPublishedLanguages($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersion(NodeInterface $node, $language = null)
    {
        return $this->tree->getPublishedVersion($node, $language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedAt(NodeInterface $node, $language = null)
    {
        return $this->tree->getPublishedAt($node, $language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersions(NodeInterface $node)
    {
        return $this->tree->getPublishedVersions($node);
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync(NodeInterface $node, $language = null)
    {
        return $this->tree->isAsync($node, $language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function findOnlineByTreeNode(NodeInterface $node)
    {
        return $this->tree->findOnlineByTreeNode($node);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneOnlineByTreeNodeAndLanguage(NodeInterface $node, $language = null)
    {
        return $this->tree->findOneOnlineByTreeNodeAndLanguage($node, $language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(NodeInterface $node, $language)
    {
        return $this->mediator->getContentDocument($node, $language ?: $this->language)->getVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getByTypeId($typeId, $type = null)
    {
        // TODO: Implement getByTypeId() method.
    }

    /**
     * {@inheritdoc}
     */
    public function hasByTypeId($typeId, $type = null)
    {
        // TODO: Implement hasByTypeId() method.
    }

    /**
     * @var bool
     */
    private $preview = false;

    /**
     * @param bool $preview
     *
     * @return $this
     */
    public function setPreview($preview = true)
    {
        $this->preview = $preview;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isViewable(NodeInterface $node, $language = null)
    {
        $isPublished = true;
        if (1) {
            $isPublished = $node->getTree()->isPublished($node, $language ?: $this->language);
        }

        return $this->mediator->isViewable($node) && $node->getInNavigation() && $isPublished;
    }

    /**
     * {@inheritdoc}
     */
    public function hasViewableChildren(NodeInterface $node, $language = null)
    {
        foreach ($this->getChildren($node) as $childNode) {
            if ($this->isViewable($childNode, $language)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(NodeInterface $node, $language = null)
    {
        return $this->mediator->getContentDocument($node, $language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(NodeInterface $node)
    {
        return $this->mediator->getTemplate($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getField(NodeInterface $node, $field, $language = null)
    {
        return $this->mediator->getField($node, $field, $language ?: $this->language);
    }
}
