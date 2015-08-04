<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\File\Parser;

use FluentDOM\Document;
use FluentDOM\Element;
use Phlexible\Component\Elementtype\Domain\Elementtype;
use Phlexible\Component\Elementtype\Domain\ElementtypeStructure;
use Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode;

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
        $id = $dom->documentElement->getAttribute('id');
        $name = $dom->documentElement->getAttribute('name');
        $revision = (int) $dom->documentElement->getAttribute('revision');
        $type = $dom->documentElement->getAttribute('type');
        $icon = $dom->documentElement->getAttribute('icon');
        $defaultTab = (string) $dom->documentElement->getAttribute('defaultTab');
        $deleted = (bool) $dom->documentElement->getAttribute('deleted');
        $hideChildren = (bool) $dom->documentElement->getAttribute('hideChildren');
        $noIndex = (bool) $dom->documentElement->getAttribute('noIndex');

        $commentNodes = $dom->documentElement->getElementsByTagName('comment');
        $comment = $commentNodes->length ? $commentNodes->item(0)->textContent : '';

        $templateNodes = $dom->documentElement->getElementsByTagName('template');
        $template = $templateNodes->length ? $templateNodes->item(0)->textContent : '';

        $metasetIdNodes = $dom->documentElement->getElementsByTagName('metasetId');
        $metasetId = $metasetIdNodes->length ? $metasetIdNodes->item(0)->textContent : '';

        $defaultContentTabNodes = $dom->documentElement->getElementsByTagName('defaultContentTab');
        $defaultContentTab = $defaultContentTabNodes->length ? $defaultContentTabNodes->item(0)->textContent : null;
        if ($defaultContentTab === '') {
            $defaultContentTab = null;
        }

        $createdAtNodes = $dom->documentElement->getElementsByTagName('createdAt');
        $createdAt = $createdAtNodes->length ? $createdAtNodes->item(0)->textContent : '';

        $createUserNodes = $dom->documentElement->getElementsByTagName('createUser');
        $createUser = $createUserNodes->length ? $createUserNodes->item(0)->textContent : '';

        $modifiedAtNodes = $dom->documentElement->getElementsByTagName('modifiedAt');
        $modifiedAt = $modifiedAtNodes->length ? $modifiedAtNodes->item(0)->textContent : '';

        $modifyUserNodes = $dom->documentElement->getElementsByTagName('modifyUser');
        $modifyUser = $modifyUserNodes->length ? $modifyUserNodes->item(0)->textContent : '';

        $mappings = array();
        $mappingNodes = $dom->documentElement->evaluate('mappings/mapping');
        if ($mappingNodes->length) {
            foreach ($mappingNodes as $mappingNode) {
                /* @var $mappingNode Element */
                $key = $mappingNode->getAttribute('key');
                $pattern = $mappingNode->getAttribute('pattern');
                $fields = array();
                $fieldNodes = $dom->xpath()->evaluate('fields/field', $mappingNode);
                if ($fieldNodes->length) {
                    foreach ($fieldNodes as $fieldNode) {
                        /* @var $fieldNode Element */
                        $field = array(
                            'dsId'  => (string) $fieldNode->getAttribute('dsId'),
                            'title' => (string) $fieldNode->getAttribute('title'),
                        );
                        if ($fieldNode->hasAttribute('index')) {
                            $field['index'] = (int) $fieldNode->getAttribute('index');
                        }
                        if ($fieldNode->hasAttribute('type')) {
                            $field['type'] = (string) $fieldNode->getAttribute('type');
                        }
                        $fields[] = $field;
                    }
                }
                $mappings[$key] = array(
                    'pattern' => $pattern,
                    'fields'  => $fields,
                );
            }
        }

        $elementtypeStructure = $this->loadStructure($dom);

        $elementtype = new Elementtype();
        $elementtype
            ->setId($id)
            ->setName($name)
            ->setType($type)
            ->setIcon($icon)
            ->setMetaSetId($metasetId)
            ->setTemplate($template)
            ->setComment($comment)
            ->setMappings($mappings)
            ->setRevision($revision)
            ->setDefaultTab($defaultTab)
            ->setDefaultContentTab($defaultContentTab)
            ->setHideChildren($hideChildren)
            ->setNoIndex($noIndex)
            ->setDeleted($deleted)
            ->setStructure($elementtypeStructure)
            ->setCreatedAt(new \DateTime($createdAt))
            ->setCreateUser($createUser)
            ->setModifiedAt(new \DateTime($modifiedAt))
            ->setModifyUser($modifyUser);

        return $elementtype;
    }

    /**
     * @param Document $dom
     *
     * @return \Phlexible\Component\Elementtype\Domain\ElementtypeStructure
     */
    private function loadStructure(Document $dom)
    {
        $elementtypeStructure = new ElementtypeStructure();

        $nodes = $dom->documentElement->evaluate('structure/node');
        if ($nodes->length) {
            foreach ($nodes as $node) {
                $this->loadNode($node, $elementtypeStructure);
            }
        }

        return $elementtypeStructure;
    }

    /**
     * @param Element                  $node
     * @param \Phlexible\Component\Elementtype\Domain\ElementtypeStructure     $elementtypeStructure
     * @param ElementtypeStructureNode $parentNode
     * @param bool                     $isReferenced
     */
    private function loadNode(Element $node, ElementtypeStructure $elementtypeStructure, ElementtypeStructureNode $parentNode = null, $isReferenced = false)
    {
        $type = $node->getAttribute('type');
        $dsId = $node->getAttribute('dsId');
        $name = $node->getAttribute('name');
        $referenceElementtypeId = $node->hasAttribute('referenceElementtypeId') ? $node->getAttribute('referenceElementtypeId') : null;

        if ($referenceElementtypeId) {
            $elementtypeStructure->addReference($referenceElementtypeId);
        }

        $labels = array();
        $labelNodes = $node->evaluate('labels/label');
        if ($labelNodes->length) {
            foreach ($labelNodes as $labelNode) {
                /* @var $labelNode Element */
                $labelType = $labelNode->getAttribute('type');
                $language = $labelNode->getAttribute('language');
                $labels[$labelType][$language] = $labelNode->textContent;
            }
        }

        $configuration = array();
        $itemNodes = $node->evaluate('configuration/item');
        if ($itemNodes->length) {
            foreach ($itemNodes as $itemNode) {
                /* @var $itemNode Element */
                $key = $itemNode->getAttribute('key');
                $configType = $itemNode->getAttribute('type');
                $configuration[$key] = $itemNode->textContent;

                if ($configType === 'json_array') {
                    $configuration[$key] = json_decode($configuration[$key], true);
                } elseif ($configType === 'boolean') {
                    $configuration[$key] = (bool) $configuration[$key];
                } elseif ($configType === 'integer') {
                    $configuration[$key] = (int) $configuration[$key];
                } elseif ($configType === 'float') {
                    $configuration[$key] = (float) $configuration[$key];
                } elseif ($configType === 'double') {
                    $configuration[$key] = (float) $configuration[$key];
                }
            }
        }

        $validation = array();
        $constraintNodes = $node->evaluate('validation/constraint');
        if ($constraintNodes->length) {
            foreach ($constraintNodes as $constraintNode) {
                /* @var $constraintNode Element */
                $key = $constraintNode->getAttribute('key');
                $validation[$key] = $constraintNode->textContent;
            }
        }

        $comment = null;
        $commentNodes = $node->getElementsByTagName('comment');
        if ($commentNodes->length) {
            $comment = $commentNodes->item(0)->textContent;
        }

        $elementtypeStructureNode = new ElementtypeStructureNode();
        $elementtypeStructureNode
            ->setType($type)
            ->setDsId($dsId)
            ->setParentNode($parentNode)
            ->setName($name)
            ->setComment($comment)
            ->setConfiguration($configuration)
            ->setValidation($validation)
            ->setLabels($labels)
            ->setReferenceElementtypeId($referenceElementtypeId)
            ->setReferenced($isReferenced);

        $elementtypeStructure->addNode($elementtypeStructureNode);

        $childNodes = $node->evaluate('children/node');
        if ($childNodes->length) {
            foreach ($childNodes as $childNode) {
                /* @var $childNode Element */
                $this->loadNode($childNode, $elementtypeStructure, $elementtypeStructureNode, (bool) ($isReferenced || $referenceElementtypeId));
            }
        }

        $referenceNodes = $node->evaluate('references/node');
        if ($referenceNodes->length) {
            foreach ($referenceNodes as $referenceNode) {
                /* @var $referenceNode Element */
                $this->loadNode($referenceNode, $elementtypeStructure, $elementtypeStructureNode, true);
            }
        }
    }
}
