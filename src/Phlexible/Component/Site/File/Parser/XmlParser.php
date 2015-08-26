<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\File\Parser;

use FluentDOM\Document;
use FluentDOM\Element;
use Phlexible\Component\NodeType\Domain\NodeTypeConstraint;
use Phlexible\Component\Site\Domain\EntryPoint;
use Phlexible\Component\Site\Domain\Navigation;
use Phlexible\Component\Site\Domain\NodeAlias;
use Phlexible\Component\Site\Domain\Site;

/**
 * XML parser
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlParser implements ParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parseString($xml)
    {
        $dom = new Document();
        $dom->loadXML($xml);

        return $this->parse($dom);
    }

    /**
     * {@inheritdoc}
     */
    public function parse(Document $dom)
    {
        $id = (string) $dom->documentElement->getAttribute('id');

        $hostname = (string) $dom->documentElement->getAttribute('hostname');
        $createdAt = (string) $dom->documentElement->getAttribute('createdAt');
        $createdBy = (string) $dom->documentElement->getAttribute('createdBy');
        $modifiedAt = (string) $dom->documentElement->getAttribute('modifiedAt');
        $modifiedBy = (string) $dom->documentElement->getAttribute('modifiedBy');

        $entryPoints = array();
        $entryPointNodes = $dom->documentElement->evaluate('entryPoints/entryPoint');
        if ($entryPointNodes->length) {
            foreach ($entryPointNodes as $entryPointNode) {
                /* @var $entryPointNode Element */
                $name = $entryPointNode->getAttribute('name');
                $hostname = $entryPointNode->getAttribute('hostname');
                $language = $entryPointNode->getAttribute('language');
                $nodeId = $entryPointNode->getAttribute('nodeId');
                $entryPoints[] = new EntryPoint($name, $hostname, $nodeId, $language);
            }
        }

        $titles = array();
        $titleNodes = $dom->documentElement->evaluate('titles/title');
        if ($titleNodes->length) {
            foreach ($titleNodes as $titleNode) {
                /* @var $titleNode Element */
                $language = $titleNode->getAttribute('language');
                $title = $titleNode->textContent;
                $titles[$language] = $title;
            }
        }

        $properties = array();
        $propertyNodes = $dom->documentElement->evaluate('properties/property');
        if ($propertyNodes->length) {
            foreach ($propertyNodes as $propertyNode) {
                /* @var $propertyNode Element */
                $key = $propertyNode->getAttribute('key');
                $value = $propertyNode->textContent;
                $properties[$key] = $value;
            }
        }

        $nodeAliases = array();
        $nodeAliasNodes = $dom->documentElement->evaluate('nodeAliases/nodeAlias');
        if ($nodeAliasNodes->length) {
            foreach ($nodeAliasNodes as $nodeAliasNode) {
                /* @var $nodeAliasNode Element */
                $name = $nodeAliasNode->getAttribute('name');
                $nodeId = $nodeAliasNode->getAttribute('nodeId');
                $language = null;
                if ($nodeAliasNode->hasAttribute('language')) {
                    $language = $nodeAliasNode->getAttribute('language');
                }
                $nodeAliases[] = new NodeAlias($name, $nodeId, $language);
            }
        }

        $navigations = array();
        $navigationNodes = $dom->documentElement->evaluate('navigations/navigation');
        if ($navigationNodes->length) {
            foreach ($navigationNodes as $navigationNode) {
                /* @var $navigationNode Element */
                $name = $navigationNode->getAttribute('name');
                $nodeId = $navigationNode->getAttribute('nodeId');
                $maxDepth = (int) $navigationNode->getAttribute('maxDepth');
                $navigations[] = new Navigation($name, (int) $nodeId, (int) $maxDepth);
            }
        }

        $nodeConstraints = array();
        $nodeConstraintNodes = $dom->documentElement->evaluate('nodeConstraints/nodeConstraint');
        if ($nodeConstraintNodes->length) {
            foreach ($nodeConstraintNodes as $nodeConstraintNode) {
                /* @var $nodeConstraintNode Element */
                $name = $nodeConstraintNode->getAttribute('name');
                $allowed = (bool) $nodeConstraintNode->getAttribute('allowed');
                $nodeTypeNodes = $nodeConstraintNode->evaluate('nodeType');
                $nodeTypes = array();
                if ($nodeTypeNodes->length) {
                    foreach ($nodeTypeNodes as $nodeTypeNode) {
                        /* @var $nodeTypeNode Element */
                        $nodeTypes[] = (string) $nodeTypeNode->textContent;
                    }
                }

                $nodeConstraints[] = new NodeTypeConstraint($name, $allowed, $nodeTypes);
            }
        }

        $site = new Site($id);
        $site
            ->setHostname($hostname)
            ->setNavigations($navigations)
            ->setNodeAliases($nodeAliases)
            ->setEntryPoints($entryPoints)
            ->setProperties($properties)
            ->setTitles($titles)
            ->setNodeConstraints($nodeConstraints)
            ->setCreateUserId($createdBy)
            ->setCreatedAt(new \DateTime($createdAt))
            ->setModifyUserId($modifiedBy)
            ->setModifiedAt(new \DateTime($modifiedAt));

        return $site;
    }
}