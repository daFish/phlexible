<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle;

use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;
use Phlexible\Bundle\ElementtypeBundle\Model\ViabilityManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Usage\UsageManager;
use Phlexible\Bundle\GuiBundle\Util\Uuid;

/**
 * Elementtype service
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeService
{
    /**
     * @var ElementtypeManagerInterface
     */
    private $elementtypeManager;

    /**
     * @var ViabilityManagerInterface
     */
    private $viabilityManager;

    /**
     * @var UsageManager
     */
    private $usageManager;

    /**
     * @param ElementtypeManagerInterface $elementtypeManager
     * @param ViabilityManagerInterface   $viabilityManager
     * @param UsageManager                $usageManager
     */
    public function __construct(
        ElementtypeManagerInterface $elementtypeManager,
        ViabilityManagerInterface $viabilityManager,
        UsageManager $usageManager)
    {
        $this->elementtypeManager = $elementtypeManager;
        $this->viabilityManager = $viabilityManager;
        $this->usageManager = $usageManager;
    }

    /**
     * Find element type by ID
     *
     * @param int $elementtypeId
     *
     * @return Elementtype
     */
    public function findElementtype($elementtypeId)
    {
        return $this->elementtypeManager->find($elementtypeId);
    }

    /**
     * Find element type by unique ID
     *
     * @param string $type
     *
     * @return Elementtype[]
     */
    public function findElementtypeByType($type)
    {
        $elementtypes = [];
        foreach ($this->elementtypeManager->findAll() as $elementtype) {
            if ($elementtype->getType() === $type) {
                $elementtypes[] = $elementtype;
            }
        }

        return $elementtypes;
    }

    /**
     * Find all element types
     *
     * @return Elementtype[]
     */
    public function findAllElementtypes()
    {
        return $this->elementtypeManager->findAll();
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return Elementtype[]
     * @deprecated
     */
    public function findAllowedParents(Elementtype $elementtype)
    {
        throw new \Exception('removed.');
    }

    /**
     * @param Elementtype $referenceElementtype
     *
     * @return Elementtype[]
     */
    public function findElementtypesUsingReferenceElementtype(Elementtype $referenceElementtype)
    {
        $elementtypes = array();
        foreach ($this->elementtypeManager->findAll() as $elementtype) {
            if (!$elementtype->getStructure()->getRootNode()) {
                continue;
            }
            $rii = new \RecursiveIteratorIterator($elementtype->getStructure()->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($rii as $node) {
                /* @var $node ElementtypeStructureNode */
                if ($node->getReferenceElementtypeId() === $referenceElementtype->getId()) {
                    $elementtypes[] = $elementtype;
                    break;
                }
            }

        }

        return $elementtypes;
    }

    /**
     * Create a new empty Element Type
     *
     * @param string               $type
     * @param string               $uniqueId
     * @param string               $name
     * @param string               $icon
     * @param ElementtypeStructure $elementtypeStructure
     * @param array                $mappings
     * @param string               $user
     * @param bool                 $flush
     *
     * @return Elementtype
     */
    public function createElementtype(
        $type,
        $uniqueId,
        $name,
        $icon,
        ElementtypeStructure $elementtypeStructure = null,
        array $mappings = null,
        $user,
        $flush = true)
    {
        if (!$icon) {
            $icons = [
                Elementtype::TYPE_FULL            => 'artikel_list.gif',
                Elementtype::TYPE_STRUCTURE       => 'nav_haupt.gif',
                Elementtype::TYPE_LAYOUTAREA      => '_fallback.gif',
                Elementtype::TYPE_LAYOUTCONTAINER => '_fallback.gif',
                Elementtype::TYPE_PART            => 'teaser_hellblau_list.gif',
                Elementtype::TYPE_REFERENCE       => '_fallback.gif',
            ];

            $icon = $icons[$type];
        }

        $elementtype = new Elementtype();
        $elementtype
            ->setUniqueId($uniqueId)
            ->setType($type)
            ->setTitle('de', $name)
            ->setTitle('en', $name)
            ->setIcon($icon)
            ->setRevision(1)
            ->setStructure($elementtypeStructure)
            ->setMappings($mappings)
            ->setCreateUser($user)
            ->setCreatedAt(new \DateTime())
            ->setModifyUser($elementtype->getCreateUser())
            ->setModifiedAt($elementtype->getCreatedAt());

        $this->elementtypeManager->updateElementtype($elementtype);

        return $elementtype;
    }

    /**
     * @param Elementtype $elementtype
     */
    public function updateElementtype(Elementtype $elementtype)
    {
        $this->elementtypeManager->updateElementtype($elementtype);
    }

    /**
     * @param Elementtype $elementtype
     * @param array       $parentIds
     */
    public function updateViability(Elementtype $elementtype, array $parentIds)
    {
        $this->viabilityManager->updateViability($elementtype, $parentIds);
    }

    /**
     * Delete an Element Type
     *
     * @param Elementtype $elementtype
     */
    public function deleteElementtype(Elementtype $elementtype)
    {
        $this->elementtypeManager->deleteElementtype($elementtype);
    }

    /**
     * Duplicate an elementtype
     *
     * @param Elementtype $sourceElementtype
     * @param string      $user
     *
     * @return Elementtype
     */
    public function duplicateElementtype(Elementtype $sourceElementtype, $user)
    {
        $elementtype = clone $sourceElementtype;
        $uniqId = uniqid();

        foreach ($elementtype->getTitles() as $language => $title) {
          $elementtype->setTitle($language, $title . ' - copy - ' . $uniqId);
        }

        $elementtypeStructure = new ElementtypeStructure();

        $elementtype
            ->setId(null)
            ->setUniqueId($elementtype->getUniqueId() . '-' . $uniqId)
            ->setRevision(1)
            ->setStructure($elementtypeStructure)
            ->setCreatedAt(new \DateTime())
            ->setCreateUser($user);

        $rii = new \RecursiveIteratorIterator($sourceElementtype->getStructure(), \RecursiveIteratorIterator::SELF_FIRST);

        $dsIdMap = [];
        foreach ($rii as $sourceNode) {
            /* @var $sourceNode ElementtypeStructureNode */
            if ($sourceNode->isReferenced()) {
                continue;
            }

            $node = clone $sourceNode;

            $dsIdMap[$sourceNode->getDsId()] = $dsId = Uuid::generate();

            $parentDsId = null;
            if (!$sourceNode->isRoot()) {
                $parentDsId = $dsIdMap[$sourceNode->getParentNode()->getDsId()];
            }

            $node
                ->setDsId($dsId)
                ->setParentDsId($parentDsId);

            $elementtypeStructure->addNode($node);
        }

        $mappings = $elementtype->getMappings();
        foreach ($mappings as $mappingIndex => $mapping) {
            foreach ($mapping['fields'] as $mappingFieldIndex => $mapingField) {
                if (isset($dsIdMap[$mapingField['dsId']])) {
                    $mappings[$mappingIndex]['fields'][$mappingFieldIndex]['dsId'] = $dsIdMap[$mapingField['dsId']];
                }
            }
        }
        $elementtype->setMappings($mappings);

        $this->elementtypeManager->updateElementtype($elementtype);

        return $elementtype;
    }
}
