<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Model;

use Phlexible\Bundle\ElementtypeBundle\Exception\InvalidArgumentException;

/**
 * Elementtype structure
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeStructure implements \Countable, \IteratorAggregate
{
    /**
     * @var string
     */
    private $rootDsId;

    /**
     * @var array
     */
    private $dsIdMap = array();

    /**
     * @var array
     */
    private $childrenMap = array();

    /**
     * @var array
     */
    private $parentMap = array();

    /**
     * @var array
     */
    private $referenceIds = array();

    /**
     * @param string $referenceId
     *
     * @return $this
     */
    public function addReferenceId($referenceId)
    {
        $this->referenceIds[] = $referenceId;
        $this->referenceIds = array_unique($this->referenceIds);

        return $this;
    }

    /**
     * @return array
     */
    public function getReferenceIds()
    {
        return $this->referenceIds;
    }

    /**
     * @param ElementtypeStructureNode $node
     *
     * @throws InvalidArgumentException
     * @return $this
     */
    public function addNode(ElementtypeStructureNode $node)
    {
        $dsId = $node->getDsId();
        $parentDsId = $node->getParentDsId();

        if (array_key_exists($dsId, $this->dsIdMap)) {
            $msg = "Duplicate node in element structure tree: ds_id=$dsId";
            throw new InvalidArgumentException($msg);
        }

        // add node by node id and ds id to node map
        $this->dsIdMap[$dsId] = $node;
        $this->childrenMap[$parentDsId][] = $dsId;
        $this->parentMap[$dsId] = $parentDsId;

        if (!$this->rootDsId && $node->isRoot()) {
            $this->rootDsId = $dsId;
        }

        return $this;
    }

    /**
     * Get parent node by node id.
     *
     * @param string $dsId
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function getParentDsId($dsId)
    {
        if (!array_key_exists($dsId, $this->parentMap)) {
            throw new InvalidArgumentException('Unknown ds_id ', $dsId);
        }

        return $this->parentMap[$dsId];
    }

    /**
     * Get parent node by node ds_id.
     *
     * @param string $dsId
     *
     * @return ElementtypeStructureNode
     */
    public function getParentNode($dsId)
    {
        $parentDsId = $this->getParentDsId($dsId);

        if (null === $parentDsId) {
            return null;
        }

        return $this->dsIdMap[$parentDsId];
    }

    /**
     * @param string $dsId
     *
     * @return ElementtypeStructureNode
     */
    public function getNode($dsId)
    {
        if (!$this->hasNode($dsId)) {
            return null;
        }

        return $this->dsIdMap[$dsId];
    }

    /**
     * @param string $dsId
     *
     * @return bool
     */
    public function hasNode($dsId)
    {
        return array_key_exists($dsId, $this->dsIdMap);
    }

    /**
     * @return string
     */
    public function getRootDsId()
    {
        return $this->rootDsId;
    }

    /**
     * @return ElementtypeStructureNode
     */
    public function getRootNode()
    {
        $root = $this->getNode($this->rootDsId);

        return $root;
    }

    /**
     * @return array
     */
    public function getAllDsIds()
    {
        return array_keys($this->dsIdMap);
    }

    /**
     * Get ds_id of children.
     *
     * @param string $dsId  ds_id of node to fetch children
     * @param int    $level (Optional) recursion depth
     *
     * @return array
     */
    public function getChildrenDsIds($dsId, $level = 1)
    {
        $childrenDsIds = (array_key_exists($dsId, $this->childrenMap))
            ? $this->childrenMap[$dsId]
            : array();

        if (($level > 1) && count($childrenDsIds)) {
            $subChildDsIds = array($childrenDsIds);

            foreach ($childrenDsIds as $childDsId) {
                $subChildDsIds[] = $this->getChildrenDsIds($childDsId, $level - 1);
            }

            $childrenDsIds = call_user_func_array('array_merge', $subChildDsIds);
        }

        return $childrenDsIds;
    }

    /**
     * Get ds_id of all children.
     *
     * @param string $dsId ds_id of node to fetch children
     *
     * @return array
     */
    public function getAllChildrenDsIds($dsId)
    {
        $childrenDsIds = $this->getChildrenDsIds($dsId, PHP_INT_MAX);

        return $childrenDsIds;
    }

    /**
     * Get children nodes.
     *
     * @param string $dsId  ds_id of node to fetch children
     * @param int    $level (Optional) recursion depth
     *
     * @return ElementtypeStructureNode[]
     */
    public function getChildNodes($dsId, $level = 1)
    {
        $children = array();

        $childrenDsIds = $this->getChildrenDsIds($dsId, $level);
        foreach ($childrenDsIds as $childDsId) {
            $children[] = $this->getNode($childDsId);
        }

        return $children;
    }

    /**
     * Get all children nodes.
     *
     * @param string $dsId ds_id of node to fetch children
     *
     * @return ElementtypeStructureNode[]
     */
    public function getAllChildNodes($dsId)
    {
        $children = $this->getChildNodes($dsId, PHP_INT_MAX);

        return $children;
    }

    /**
     * Has node children?
     *
     * @param string $dsId (Optional) ds_id of node to check childrens
     *
     * @return bool
     */
    public function hasChildNodes($dsId = null)
    {
        if (!$dsId) {
            // use root as default
            $dsId = $this->rootDsId;
        }

        $childrenDsIds = $this->getChildrenDsIds($dsId);
        $hasChildren = count($childrenDsIds) > 0;

        return $hasChildren;
    }

    /**
     * Get number of nodes in this tree.
     *
     * @return int
     */
    public function count()
    {
        return count($this->dsIdMap);
    }

    /**
     * @param string $dsId
     *
     * @return ElementtypeStructureNode[]
     */
    public function getParentNodes($dsId)
    {
        $parents = array();

        $node = $this->getNode($dsId);
        while ($node) {
            // get parent
            $node = $this->getParentNode($node->getDsId());

            // add id
            if ($node) {
                $parents[] = $node;
            }
        }

        return $parents;
    }

    /**
     * Get the ds_ids of all fields in this structure tree of a specific type.
     *
     * @param string $fieldType
     *
     * @return array
     */
    public function getDsIdsByFieldType($fieldType)
    {
        $result = array();
        foreach ($this->dsIdMap as $dsId => $node) {
            /* @var $node ElementtypeStructureNode */
            if ($fieldType == $node->getType()) {
                $result[] = $dsId;
            }
        }

        return $result;
    }

    /**
     * Get the working titles of all fields in this structure tree of a specific type.
     *
     * @param string $fieldType
     *
     * @return array
     */
    public function getNamesByFieldType($fieldType)
    {
        $result = array();
        foreach ($this->dsIdMap as $node) {
            /* @var $node ElementtypeStructureNode */
            if ($fieldType == $node->getType()) {
                $result[] = $node->getName();
            }
        }

        return $result;
    }

    /**
     * @return ElementtypeStructureIterator
     */
    public function getIterator()
    {
        return new ElementtypeStructureIterator($this);
    }
}
