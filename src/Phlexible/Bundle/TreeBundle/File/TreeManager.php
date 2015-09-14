<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\File;

use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Bundle\TreeBundle\Exception\NodeNotFoundException;
use Phlexible\Bundle\TreeBundle\File\Parser\XmlTreeParser;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Component\Node\Model\NodeInterface;
use Phlexible\Component\Node\Model\NodeManagerInterface;
use Phlexible\Component\Site\Model\SiteManagerInterface;
use Phlexible\Component\Tree\Tree;

/**
 * File based Node manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeManager implements NodeManagerInterface
{
    /**
     * @var SiteManagerInterface
     */
    private $siterootManager;

    /**
     * @var TreeInterface[]
     */
    private $trees = array();

    /**
     * @param \Phlexible\Component\Site\Model\SiteManagerInterface $siterootManager
     */
    public function __construct(SiteManagerInterface $siterootManager)
    {
        $this->siterootManager = $siterootManager;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        $this->load();
        return $this->nodes->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        $this->load();
        $nodes = $this->findBy($criteria, $orderBy, 1, 0);

        return current($nodes);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $this->load();
        //print_r($criteria);
        $nodes = $this->nodes->filter(function($node) use ($criteria) {
            foreach ($criteria as $key => $value) {
                $get = 'get' . ucfirst($key);

                if ($node->$get() !== $value) {
                    return false;
                }
            }
            return true;
        });

        return $nodes->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function updateNode(NodeInterface $node, $flush = true)
    {
        throw new \Exception("This shouldn't be called.");
    }

    /**
     * @var ArrayCollection
     */
    private $nodes;
    /**
     * @var ArrayCollection
     */
    private $states;

    /**
     * Load
     */
    private function load()
    {
        if ($this->nodes) {
            return;
        }

        $parser = new XmlTreeParser();
        $this->nodes = new ArrayCollection();
        $this->states = new ArrayCollection();
        foreach ($this->siterootManager->findAll() as $siteroot) {
            $siterootNodes = new ArrayCollection();
            $siterootStates = new ArrayCollection();
            $parser->parse(file_get_contents('/tmp/' . $siteroot->getId() . '.xml'), $siterootNodes, $siterootStates);
            foreach ($siterootNodes as $key => $siterootNode) {
                $this->nodes->set($key, $siterootNode);
            }
            foreach ($siterootStates as $key => $siterootState) {
                $this->states->set($key, $siterootState);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBySiteRootId($siteRootId)
    {
        if (!isset($this->trees[$siteRootId])) {
            $tree = new Tree($this, $siteRootId);
            $this->trees[$siteRootId] = $tree;
        }

        return $this->trees[$siteRootId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByNodeId($nodeId)
    {
        foreach ($this->siterootManager->findAll() as $siteroot) {
            $tree = $this->getBySiteRootId($siteroot->getId());

            if ($tree->has($nodeId)) {
                return $tree;
            }
        }

        throw new NodeNotFoundException("Tree for node ID $nodeId not found.");
    }

    /**
     * {@inheritdoc}
     */
    public function getByTypeId($typeId, $type = null)
    {
        $trees = array();
        foreach ($this->siterootManager->findAll() as $siteroot) {
            $tree = $this->getBySiteRootId($siteroot->getId());

            if ($tree->hasByTypeId($typeId, $type)) {
                $trees[] = $tree;
            }
        }

        return $trees;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        foreach ($this->siterootManager->findAll() as $siteroot) {
            $this->getBySiteRootId($siteroot->getId());
        }

        return $this->trees;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished(NodeInterface $node, $language)
    {
        return $this->states->containsKey($node->getId() . '_' . $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedLanguages(NodeInterface $node)
    {
        $languages = array();
        foreach ($this->states as $state) {
            if ($state->getTreeNode() === $node) {
                $languages[] = $state->getLanguage();
            }
        }

        return $languages;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersion(NodeInterface $node, $language)
    {
        return $this->states->get($node->getId() . '_' . $language)->getVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedAt(NodeInterface $node, $language)
    {
        return $this->states->get($node->getId() . '_' . $language)->getPublishedAt();
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersions(NodeInterface $node)
    {
        $versions = array();
        foreach ($this->states as $state) {
            if ($state->getTreeNode() === $node) {
                $versions[] = $state->getVersion();
            }
        }

        return $versions;
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync(NodeInterface $node, $language)
    {
        // TODO: Implement isAsync() method.
    }

    /**
     * {@inheritdoc}
     */
    public function findOnlineByTreeNode(NodeInterface $node)
    {
        $states = array();
        foreach ($this->states as $state) {
            if ($state->getTreeNode() === $node) {
                $states[] = $state;
            }
        }

        return $states;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneOnlineByTreeNodeAndLanguage(NodeInterface $node, $language)
    {
        return $this->states->get($node->getId() . '_' . $language);
    }
}
