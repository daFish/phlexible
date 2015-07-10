<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Phlexible\Bundle\SiterootBundle\Entity\Navigation;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\NodeInterface;

/**
 * Content tree interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ContentTreeInterface extends TreeInterface
{
    /**
     * @return Siteroot
     */
    public function getSiteroot();

    /**
     * @return bool
     */
    public function isDefaultSiteroot();

    /**
     * @return Url[]
     */
    public function getUrls();

    /**
     * @return Url
     */
    public function getDefaultUrl();

    /**
     * @return Navigation[]
     */
    public function getNavigations();

    /**
     * @param string $language
     *
     * @return array
     */
    public function getSpecialTids($language = null);

    /**
     * @param NodeInterface $treeNode
     *
     * @return ContentNode
     */
    public function createContentTreeNodeFromTreeNode(NodeInterface $treeNode);

    /**
     * @param NodeInterface $node
     * @param string            $language
     *
     * @return int
     */
    public function getVersion(NodeInterface $node, $language);

    /**
     * @param NodeInterface $node
     * @param string            $language
     *
     * @return bool
     */
    public function isViewable(NodeInterface $node, $language = null);

    /**
     * @param NodeInterface $node
     *
     * @return null
     */
    public function getContent(NodeInterface $node);

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    public function getTemplate(NodeInterface $node);
}
