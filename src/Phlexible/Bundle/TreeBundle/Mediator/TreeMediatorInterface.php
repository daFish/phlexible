<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Mediator;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Node\Model\NodeInterface;

/**
 * Tree mediator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TreeMediatorInterface
{
    /**
     * @param NodeContext $node
     *
     * @return bool
     */
    public function accept(NodeContext $node);

    /**
     * @param NodeContext $node
     * @param string      $field
     * @param string      $language
     *
     * @return string
     */
    public function getField(NodeContext $node, $field, $language);

    /**
     * @param NodeContext $node
     *
     * @return array
     */
    public function getFieldMappings(NodeContext $node);

    /**
     * @param NodeContext $node
     * @param string      $language
     * @param int         $version
     *
     * @return mixed
     */
    public function getContent(NodeContext $node, $language, $version = null);

    /**
     * @param NodeContext $node
     *
     * @return array
     */
    public function getContentVersions(NodeContext $node);

    /**
     * @param NodeContext $node
     *
     * @return string
     */
    public function getTemplate(NodeContext $node);

    /**
     * @param NodeContext $node
     *
     * @return bool
     */
    public function isViewable(NodeContext $node);

    /**
     * @param mixed $contentDocument
     *
     * @return \Phlexible\Component\Node\Model\NodeInterface
     */
    public function createNodeForContentDocument($contentDocument);
}
