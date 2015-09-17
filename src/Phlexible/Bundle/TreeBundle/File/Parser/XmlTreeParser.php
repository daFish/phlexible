<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\File\Parser;

use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Component\Node\Model\NodeInterface;
use SimpleXMLElement;

/**
 * XML tree parser.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlTreeParser
{
    /**
     * @param string          $content
     * @param ArrayCollection $nodes
     */
    public function parse($content, ArrayCollection $nodes)
    {
        $xml = simplexml_load_string($content);

        $attributes = $xml->attributes();
        $siterootId = (string) $attributes['siterootId'];

        $this->parseNode($nodes, $siterootId, $xml->node);
    }

    /**
     * @param ArrayCollection    $nodes
     * @param string             $siterootId
     * @param SimpleXMLElement   $nodeNode
     * @param NodeInterface|null $parentNode
     *
     * @return NodeInterface
     *
     * @throws \Exception
     */
    private function parseNode(ArrayCollection $nodes, $siterootId, SimpleXMLElement $nodeNode, NodeInterface $parentNode = null)
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
        /* @var $node NodeInterface */
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

        if ($parentId) {
            $parentNode = $nodes->get($parentId);
            if (!$parentNode) {
                throw new \Exception("Parent node $parentId not found.");
            }
            $node->setParentNode($nodes->get($parentId));
        }

        if (count($nodeNode->node)) {
            foreach ($nodeNode->node as $childNodeNode) {
                $this->parseNode($nodes, $siterootId, $childNodeNode, $node);
            }
        }
    }
}
