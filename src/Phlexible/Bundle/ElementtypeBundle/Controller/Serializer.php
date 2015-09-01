<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Phlexible\Component\Elementtype\ElementtypeService;
use Phlexible\Component\Elementtype\Domain\Elementtype;
use Phlexible\Component\Elementtype\Domain\ElementtypeStructure;
use Phlexible\Component\Elementtype\Domain\ElementtypeStructureNode;

/**
 * Class Serializer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Serializer
{
    private $elementtypeService;

    public function __construct(ElementtypeService $elementtypeService)
    {
        $this->elementtypeService = $elementtypeService;
    }

    /**
     * @param Elementtype $elementtype
     * @param string      $language
     * @param string      $mode
     *
     * @return array
     */
    public function serialize(Elementtype $elementtype, $language, $mode = 'edit')
    {
        $elementtypeStructure = $elementtype->getStructure();

        $rootNode = $elementtypeStructure->getRootNode();
        $type = $elementtype->getType(); // != 'reference' ? 'root' : 'referenceroot';

        $children = array();
        $rootDsId = '';
        $rootType = 'root';
        if ($rootNode) {
            $rootDsId = $rootNode->getDsId();
            $rootType = $rootNode->getType();
            $children = $elementtypeStructure->getChildNodes($rootNode->getDsId());
        }

        return $this->serializeNodes(
            $elementtypeStructure,
            $children,
            $language,
            $mode,
            false,
            true
        );

        $data = array(
            'text'               => $elementtype->getTitle($language)
                . ' [v' . $elementtype->getRevision() . ', '
                . $elementtype->getType() . ']',
            'id'                 => md5(serialize($rootNode)),
            'dsId'               => $rootDsId,
            'elementtypeId'      => $elementtype->getId(),
            'elementtypeVersion' => $elementtype->getRevision(),
            'icon'               => '/bundles/phlexibleelementtype/elementtypes/' . $elementtype->getIcon(),
            'cls'                => 'p-elementtypes-type-' . $type,
            'leaf'               => false,
            'expanded'           => true,
            'type'               => $rootType,
            'allowDrag'          => ($type == Elementtype::TYPE_REFERENCE),
            'allowDrop'          => $mode == 'edit',
            'editable'           => $mode == 'edit',
            'properties'         => array(
                'title'             => $elementtype->getTitle($language),
                'referenceTitle'    => "{$elementtype->getTitle($language)} [v{$elementtype->getRevision()}]",
                'uniqueId'          => $elementtype->getUniqueId(),
                'icon'              => $elementtype->getIcon(),
                'hideChildren'      => $elementtype->getHideChildren() ? 'on' : '',
                'defaultTab'        => $elementtype->getDefaultTab(),
                'defaultContentTab' => $elementtype->getDefaultContentTab(),
                'type'              => $type,
                'template'          => $elementtype->getTemplate(),
                'metaset'           => $elementtype->getMetaSetId(),
                'comment'           => $elementtype->getComment(),
            ),
            'mappings' => $elementtype->getMappings(),
            'children' => $this->serializeNodes(
                $elementtypeStructure,
                $children,
                $language,
                $mode,
                false,
                true
            )
        );

        return $data;
    }

    /**
     * Build an Element Type data tree
     *
     * @param ElementtypeStructure       $structure
     * @param ElementtypeStructureNode[] $nodes
     * @param string                     $language
     * @param string                     $mode
     * @param bool                       $reference
     * @param bool                       $allowDrag
     *
     * @return array
     */
    private function serializeNodes(
        ElementtypeStructure $structure,
        array $nodes,
        $language,
        $mode = 'edit',
        $reference = false,
        $allowDrag = true)
    {
        $return = array();

        foreach ($nodes as $node) {
            /* @var $node ElementtypeStructureNode */

            $tmp = array(
                'id'         => md5(serialize($node)),
                'text'       => $node->getLabel('fieldLabel', $language) . ' (' . $node->getName() . ')',
                'dsId'       => $node->getDsId(),
                'cls'        => 'p-elementtypes-node p-elementtypes-type-' . $node->getType()
                    . ($reference ? ' p-elementtypes-reference' : ''),
                'leaf'       => true,
                'expanded'   => false,
                'type'       => $node->getType(),
                'reference'  => $reference,
                'allowDrag'  => $allowDrag,
                'allowDrop'  => $mode == 'edit' && !$reference,
                'editable'   => $mode == 'edit' || !$reference,
                'properties' => array(
                    'title'   => $node->getName(),
                    'type'    => $node->getType(),
                    'comment' => $node->getComment(),
                    'image'   => '',
                ),
                'configuration'    => $node->getConfiguration(),
                'labels'           => $node->getLabels(),
                'validation'       => $node->getValidation()
            );

            if ($structure->hasChildNodes($node->getDsId())) {
                $tmp['leaf'] = false;
                $tmp['expanded'] = true;
                $tmp['children'] = $this->serializeNodes(
                    $structure,
                    $structure->getChildNodes($node->getDsId()),
                    $language,
                    $mode,
                    $reference
                );
            }

            if ($node->isReference()) {
                $referenceElementtype = $this->elementtypeService->findElementtype($node->getReferenceElementtypeId());
                $children = $structure->getChildNodes($node->getDsId());
                $referenceRoot = $children[0];

                $tmp['text'] = $referenceElementtype->getName() . ' [v' . $referenceElementtype->getRevision() . ']';
                $tmp['leaf'] = false;
                $tmp['expanded'] = true;
                $tmp['reference'] = array('refID' => $referenceElementtype->getId(), 'refVersion' => $referenceElementtype->getRevision());
                $tmp['editable'] = false;
                $tmp['allowDrag'] = true;
                $tmp['children'] = $this->serializeNodes(
                    $structure,
                    $structure->getChildNodes($referenceRoot->getDsId()),
                    $language,
                    'template',
                    true,
                    true
                );
                //                $tmp['cls'] = 'p-elementtypes-type-reference';
            }

            $return[] = $tmp;
        }

        return $return;
    }

}
