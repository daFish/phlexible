<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\File\Parser;

use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Bundle\TreeBundle\Entity\NodeState;
use Phlexible\Component\Node\Model\NodeInterface;

/**
 * XML tree parser
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlTreeParser
{
    /**
     * @param string          $content
     * @param ArrayCollection $nodes
     * @param ArrayCollection $states
     */
    public function parse($content, ArrayCollection $nodes, ArrayCollection $states)
    {
        $xml = simplexml_load_string($content);

        $siterootId = (string) $xml->attributes()['siterootId'];

        $this->parseNode($nodes, $states, $siterootId, $xml->node);
    }

    /**
     * @param ArrayCollection        $nodes
     * @param ArrayCollection        $states
     * @param string                 $siterootId
     * @param \SimpleXMLElement      $nodeNode
     * @param \Phlexible\Component\Node\Model\NodeInterface|null $parentNode
     *
     * @return \Phlexible\Component\Node\Model\NodeInterface
     * @throws \Exception
     */
    private function parseNode(ArrayCollection $nodes, ArrayCollection $states, $siterootId, \SimpleXMLElement $nodeNode, NodeInterface $parentNode = null)
    {
        $attr = $nodeNode->attributes();
        $id = (int) $attr['id'];
        $parentId = null;
        if ($attr['parentId']) {
            $parentId = (int) $attr['parentId'];
        }
        $nodeType = (string) $attr['nodeType'];
        $type = (string) $attr['type'];
        $typeId = (string) $attr['typeId'];
        $sort = (string) $attr['sort'];
        $sortMode = (string) $attr['sortMode'];
        $sortDir = (string) $attr['sortDir'];
        $inNavigation = (bool) $attr['inNavigation'];
        $createUserId = (string) $attr['createUserId'];
        $createdAt = new \DateTime((string) $attr['createdAt']);

        $attributes = array();
        foreach ($nodeNode->attributes->attribute as $attributeNode) {
            $attr = $attributeNode->attributes();
            $key = (string) $attr['key'];
            $value = $attributeNode->content;
            $attributes[$key] = $value;
        }

        $node = new $nodeType();
        /* @var $node \Phlexible\Component\Node\Model\NodeInterface */
        $node->setId($id);
        $node->setSiterootId($siterootId);
        $node->setParentNode($parentNode);
        $node->setContentType($type);
        $node->setContentId($typeId);
        $node->setSort($sort);
        $node->setSortMode($sortMode);
        $node->setSortDir($sortDir);
        $node->setInNavigation($inNavigation);
        $node->setCreatedAt($createdAt);
        $node->setCreateUserId($createUserId);

        $nodes->set($node->getId(), $node);

        foreach ($nodeNode->states->state as $stateNode) {
            $attr = $stateNode->attributes();
            $version = (string) $attr['version'];
            $language = (string) $attr['language'];
            $hash = (string) $attr['hash'];
            $publishUser = (string) $attr['publishUser'];
            $publishedAt = new \DateTime((string) $attr['publishedAt']);

            $state = new NodeState();
            $state->setHash($hash);
            $state->setVersion($version);
            $state->setLanguage($language);
            $state->setPublishedAt($publishedAt);
            $state->setPublishUserId($publishUser);
            $state->setNode($node);

            $states->set($id . '_' . $language, $state);
        }

        if ($parentId) {
            $parentNode = $nodes->get($parentId);
            if (!$parentNode) {
                throw new \Exception("Parent node $parentId not found.");
            }
            $node->setParentNode($nodes->get($parentId));
        }

        if (count($nodeNode->node)) {
            foreach ($nodeNode->node as $childNodeNode) {
                $this->parseNode($nodes, $states, $siterootId, $childNodeNode, $node);
            }
        }
    }
}
