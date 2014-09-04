<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Model;

use Phlexible\Bundle\TreeBundle\Entity\TreeNodeOnline;

/**
 * State manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface StateManagerInterface
{
    /**
     * @param TreeNodeInterface $treeNode
     *
     * @return TreeNodeOnline[]
     */
    public function findByTreeNode(TreeNodeInterface $treeNode);

    /**
     * @param TreeNodeInterface $treeNode
     * @param string            $language
     *
     * @return TreeNodeOnline
     */
    public function findOneByTreeNodeAndLanguage(TreeNodeInterface $treeNode, $language);

    /**
     * @param TreeNodeInterface $treeNode
     * @param string            $language
     *
     * @return bool
     */
    public function isPublished(TreeNodeInterface $treeNode, $language);

    /**
     * @param TreeNodeInterface $treeNode
     *
     * @return array
     */
    public function getPublishedLanguages(TreeNodeInterface $treeNode);

    /**
     * @param TreeNodeInterface $treeNode
     *
     * @return array
     */
    public function getPublishedVersions(TreeNodeInterface $treeNode);

    /**
     * @param TreeNodeInterface $treeNode
     * @param string            $language
     *
     * @return int
     */
    public function getPublishedVersion(TreeNodeInterface $treeNode, $language);

    /**
     * @param TreeNodeInterface $treeNode
     * @param string            $language
     *
     * @return bool
     */
    public function isAsync(TreeNodeInterface $treeNode, $language);

    /**
     * @param TreeNodeInterface $treeNode
     * @param int               $version
     * @param string            $language
     * @param string            $userId
     * @param string|null       $comment
     *
     * @return
     */
    public function publish(TreeNodeInterface $treeNode, $version, $language, $userId, $comment = null);

    /**
     * @param TreeNodeInterface $treeNode
     * @param string            $language
     */
    public function setOffline(TreeNodeInterface $treeNode, $language);
}
