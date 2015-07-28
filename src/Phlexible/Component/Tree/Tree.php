<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Tree;

use Phlexible\Bundle\TreeBundle\Entity\NodeState;
use Phlexible\Bundle\TreeBundle\Event\MoveNodeContextEvent;
use Phlexible\Bundle\TreeBundle\Event\NodeContextEvent;
use Phlexible\Bundle\TreeBundle\Event\PublishNodeContextEvent;
use Phlexible\Bundle\TreeBundle\Event\ReorderChildNodesContextEvent;
use Phlexible\Bundle\TreeBundle\Event\ReorderNodeContextEvent;
use Phlexible\Bundle\TreeBundle\Event\SetNodeOfflineContextEvent;
use Phlexible\Bundle\TreeBundle\Exception\InvalidNodeMoveException;
use Phlexible\Bundle\TreeBundle\Model\NodeStateManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Bundle\TreeBundle\Node\NodeContextFactoryInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeHasherInterface;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Phlexible\Component\Node\Model\NodeManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Tree implements TreeInterface
{
    /**
     * @var string
     */
    private $siterootId;

    /**
     * @var \Phlexible\Component\Node\Model\NodeManagerInterface
     */
    private $nodeManager;

    /**
     * @var NodeStateManagerInterface
     */
    private $nodeStateManager;

    /**
     * @var NodeContextFactoryInterface
     */
    private $nodeContextFactory;

    /**
     * @var NodeHasherInterface
     */
    private $nodeHasher;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param string                      $siterootId
     * @param NodeManagerInterface        $nodeManager
     * @param NodeStateManagerInterface   $nodeStateManager
     * @param NodeContextFactoryInterface $nodeContextFactory
     * @param NodeHasherInterface         $nodeHasher
     * @param EventDispatcherInterface    $eventDispatcher
     */
    public function __construct(
        $siterootId,
        NodeManagerInterface $nodeManager,
        NodeStateManagerInterface $nodeStateManager,
        NodeContextFactoryInterface $nodeContextFactory,
        NodeHasherInterface $nodeHasher,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->siterootId = $siterootId;
        $this->nodeManager = $nodeManager;
        $this->nodeStateManager = $nodeStateManager;
        $this->nodeContextFactory = $nodeContextFactory;
        $this->nodeHasher = $nodeHasher;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     *
     * @return TreeIterator
     */
    public function getIterator()
    {
        return new TreeIterator($this);
    }

    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * {@inheritdoc}
     */
    public function setDefaultLanguage($defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function getSiterootId()
    {
        return $this->siterootId;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        if (!isset($this->nodes['root'])) {
            $node = $this->nodeManager->findOneByNodeType(
                array('PhlexibleTreeBundle:PageNode', 'PhlexibleTreeBundle:StructureNode'),
                array('siterootId' => $this->siterootId, 'parentNode' => null)
            );
            $this->nodes['root'] = $this->nodeContextFactory->factory($this, $node, $this->defaultLanguage);
        }

        return $this->nodes['root'];
    }

    /**
     * @var NodeContext[]
     */
    private $nodes = array();

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (!isset($this->nodes[$id]) || $this->nodes[$id] === null) {
            $node = $this->nodeManager->findOneBy(
                array('siterootId' => $this->siterootId, 'id' => $id)
            );
            if ($node) {
                $this->nodes[$id] = $this->nodeContextFactory->factory($this, $node, $this->defaultLanguage);
            } else {
                $this->nodes[$id] = null;
            }
        }

        return $this->nodes[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return $this->get($id) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren(NodeContext $node, $types = array('PhlexibleTreeBundle:PageNode', 'PhlexibleTreeBundle:StructureNode'))
    {
        $rawNodes = $this->nodeManager->findByNodeType(
            $types,
            array('siterootId' => $this->siterootId, 'parentNode' => $node->getId()),
            array('sort' => 'ASC')
        );

        $nodes = array();
        foreach ($rawNodes as $node) {
            if (!isset($this->nodes[$node->getId()])) {
                $this->nodes[$node->getId()] = $this->nodeContextFactory->factory($this, $node, $this->defaultLanguage);
            }
            $nodes[] = $this->nodes[$node->getId()];
        }

        return $nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren(NodeContext $node, $types = array('PhlexibleTreeBundle:PageNode', 'PhlexibleTreeBundle:StructureNode'))
    {
        return count($this->getChildren($node, $types)) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(NodeContext $node)
    {
        $parentNode = $node->getNode()->getParentNode();
        if (!$parentNode) {
            return null;
        }
        $parentId = $parentNode->getId();
        if (!isset($this->nodes[$parentId]) || $this->nodes[$parentId] === null) {
            $node = $this->nodeManager->find($parentId);
            $this->nodes[$parentId] = $this->nodeContextFactory->factory($this, $node, $this->defaultLanguage);
        }

        return $this->nodes[$parentId];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdPath(NodeContext $node)
    {
        $ids = array();
        foreach ($this->getPath($node) as $pathNode) {
            $ids[] = $pathNode->getId();
        }

        return $ids;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(NodeContext $node)
    {
        $path = array();

        do {
            $path[$node->getId()] = $node;
        } while ($node = $this->getParent($node));

        $path = array_reverse($path, true);

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot(NodeContext $node)
    {
        return $this->getRoot() === $node;
    }

    /**
     * {@inheritdoc}
     */
    public function isChildOf(NodeContext $childNode, NodeContext $parentNode)
    {
        $path = $this->getIdPath($childNode);

        foreach ($path as $id) {
            if ($parentNode->getId() === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isParentOf(NodeContext $parentNode, NodeContext $childNode)
    {
        return $this->isChildOf($childNode, $parentNode);
    }

    /**
     * {@inheritdoc}
     */
    public function isInstance(NodeContext $node)
    {
        return $this->nodeManager->isInstance($node->getNode());
    }

    /**
     * {@inheritdoc}
     */
    public function isInstanceMaster(NodeContext $node)
    {
        return $node->getAttribute('instanceMaster', false);
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished(NodeContext $node, $language = null)
    {
        return $this->nodeStateManager->findOneBy(array('node' => $node->getNode(), 'language' => $language ?: $this->defaultLanguage)) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedLanguages(NodeContext $node)
    {
        $languages = array();
        foreach ($this->nodeStateManager->findBy(array('node' => $node->getNode())) as $state) {
            $languages[] = $state->getLanguage();
        }

        return $languages;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersion(NodeContext $node, $language = null)
    {
        $state = $this->nodeStateManager->findOneBy(array('node' => $node->getNode(), 'language' => $language ?: $this->defaultLanguage));
        if (!$state) {
            return null;
        }

        return $state->getVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersions(NodeContext $node)
    {
        $versions = array();
        foreach ($this->nodeStateManager->findBy(array('node' => $node->getNode())) as $state) {
            $versions[$state->getLanguage()] = $state->getVersion();
        }

        return $versions;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedAt(NodeContext $node, $language = null)
    {
        $state = $this->nodeStateManager->findOneBy(array('node' => $node->getNode(), 'language' => $language ?: $this->defaultLanguage));
        if (!$state) {
            return null;
        }

        return $state->getPublishedAt();
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishUserId(NodeContext $node, $language = null)
    {
        $state = $this->nodeStateManager->findOneBy(array('node' => $node->getNode(), 'language' => $language ?: $this->defaultLanguage));
        if (!$state) {
            return null;
        }

        return $state->getPublishUserId();
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync(NodeContext $node, $language = null)
    {
        $state = $this->nodeStateManager->findOneBy(array('node' => $node->getNode(), 'language' => $language ?: $this->defaultLanguage));
        if (!$state) {
            return false;
        }

        $versions = $node->getContentVersions();
        sort($versions);
        $version = end($versions);

        if ($version === $state->getVersion()) {
            return false;
        }

        $publishedHash = $state->getHash();
        $currentHash = $this->nodeHasher->hashNode($node, $version, $language ?: $this->defaultLanguage);

        return $publishedHash === $currentHash;
    }

    /**
     * {@inheritdoc}
     */
    public function updateNode(NodeContext $node)
    {
        $this->nodeManager->updateNode($node->getNode());
    }

    /**
     * {@inheritdoc}
     */
    public function createNode(
        NodeContext $parentNode,
        NodeContext $afterNode = null,
        $contentDocument,
        array $attributes,
        $userId,
        $sortMode = 'free',
        $sortDir = 'asc',
        $navigation = false,
        $needAuthentication = false)
    {
        $sort = 0;
        $sortNodes = array();
        if ($parentNode->getSortMode() === 'free') {
            if ($afterNode) {
                $sort = $afterNode->getSort() + 1;
            }

            foreach ($this->nodeManager->findBy(array('parentNode' => $parentNode->getNode())) as $sortNode) {
                if ($sortNode->getSort() >= $sort) {
                    $sortNode->setSort($sortNode->getSort() + 1);
                    $sortNodes[] = $sortNode;
                }
            }
        }

        $node = $this->mediator->createNodeForContentDocument($contentDocument);
        $node
            ->setSiterootId($this->siterootId)
            ->setParentNode($parentNode->getNode())
            ->setAttributes($attributes)
            ->setSort($sort)
            ->setSortMode($sortMode)
            ->setSortDir($sortDir)
            ->setInNavigation($navigation)
            ->setNeedAuthentication($needAuthentication)
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime);

        $this->nodeManager->updateNode($node);

        foreach ($sortNodes as $sortNode) {
            $this->nodeManager->updateNode($sortNode);
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function createNodeInstance(
        NodeContext $parentNode,
        NodeContext $afterNode = null,
        NodeContext $sourceNode,
        $userId
    )
    {
        $sort = 0;
        $sortNodes = array();
        if ($parentNode->getSortMode() === 'free') {
            if ($afterNode) {
                $sort = $afterNode->getSort() + 1;
            }

            foreach ($this->getChildren($parentNode) as $sortNode) {
                if ($sortNode->getSort() >= $sort) {
                    $sortNode->setSort($sortNode->getSort() + 1);
                    $sortNodes[] = $sortNode;
                }
            }
        }

        $nodeClass = get_class($sourceNode);

        $node = new $nodeClass();
        $node
            ->setSiterootId($this->siterootId)
            ->setParentNode($parentNode->getNode())
            ->setType($sourceNode->getContentType())
            ->setTypeId($sourceNode->getContentId())
            ->setAttributes($sourceNode->getAttributes())
            ->setSort($sort)
            ->setSortMode($sourceNode->getSortMode())
            ->setSortDir($sourceNode->getSortDir())
            ->setCreateUserId($userId)
            ->setCreatedAt(new \DateTime);

        $this->nodeManager->updateNode($node, false);

        foreach ($sortNodes as $sortNode) {
            $this->nodeManager->updateNode($sortNode, false);
        }

        return $node;
    }

    /**
     * Reorders node after beforeNode
     *
     * {@inheritdoc}
     */
    public function reorder(NodeContext $node, NodeContext $beforeNode)
    {
        if ($beforeNode->getParent()->getId() !== $node->getParent()->getId()) {
            throw new InvalidNodeMoveException('Node and targetNode need to have the same parent.');
        }

        if ($node->getParent()->getSortMode() !== 'free') {
            return;
        }

        $sort = $beforeNode->getSort() + 1;

        $event = new ReorderNodeContextEvent($node, $sort);
        if ($this->eventDispatcher->dispatch(TreeEvents::BEFORE_REORDER_NODE_CONTEXT, $event)->isPropagationStopped()) {
            return;
        }

        $updatesNodes = array();

        $currentSort = $sort + 1;
        foreach ($this->getChildren($node->getParent()) as $childNode) {
            if ($childNode->getSort() <= $sort) {
                $childNode->getNode()->setSort($currentSort++);
                $updatesNodes[] = $childNode;
            }
        }

        $node->getNode()->setSort($sort);
        $updateNodes[] = $node;

        foreach ($updateNodes as $updateNode) {
            $this->nodeManager->updateNode($updateNode);
        }

        $event = new NodeContextEvent($node);
        $this->eventDispatcher->dispatch(TreeEvents::REORDER_NODE_CONTEXT, $event);

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function reorderChildren(NodeContext $node, array $sortIds)
    {
        if (count($this->getChildren($node)) !== count($sortIds)) {
            throw new InvalidNodeMoveException('Children count mismatch.');
        }

        $childNodes = array();
        foreach ($sortIds as $sort => $nodeId) {
            $childNode = $this->get($nodeId);
            if ($childNode->getParent()->getId() !== $node->getId()) {
                throw new InvalidNodeMoveException('Node and targetNode need to have the same parent.');
            }
            $childNodes[$sort] = $childNode;
        }

        $event = new ReorderChildNodesContextEvent($node, $sortIds);
        if ($this->eventDispatcher->dispatch(TreeEvents::BEFORE_REORDER_CHILD_NODES_CONTEXT, $event)->isPropagationStopped()) {
            return;
        }

        foreach ($childNodes as $index => $childNode) {
            $childNode->getNode()->setSort($index);

            $this->nodeManager->updateNode($childNode->getNode());
        }

        $event = new ReorderChildNodesContextEvent($node, $sortIds);
        $this->eventDispatcher->dispatch(TreeEvents::REORDER_CHILD_NODES_CONTEXT, $event);

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function move(NodeContext $node, NodeContext $toNode, $userId)
    {
        if ($this->isChildOf($toNode, $node)) {
            throw new InvalidNodeMoveException('Invalid move.');
        }

        $oldParentId = $node->getParent()->getId();

        $event = new MoveNodeContextEvent($node, $toNode);
        if ($this->eventDispatcher->dispatch(TreeEvents::BEFORE_MOVE_NODE_CONTEXT, $event)->isPropagationStopped()) {
            return;
        }

        $node->getNode()
            ->setSort(0)
            ->setParentNode($toNode->getNode());

        $this->nodeManager->updateNode($node->getNode());

        // TODO: sort
        //$this->sorter->sortNode($toNode);

        $event = new MoveNodeContextEvent($node, $toNode);
        $this->eventDispatcher->dispatch(TreeEvents::MOVE_NODE_CONTEXT, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(NodeContext $node, $userId, $comment = null)
    {
        // TODO: listener
        /*
        $rightsIdentifiers = array(
            array('uid' => $uid)
        );
        foreach (MWF_Env::getUser()->getGroups() as $group)
        {
            $rightsIdentifiers[] = array('gid' => $group->getId());
        }
        $this->_deleteCheck($node, $rightsIdentifiers);
        */

        $this->doDelete($node, $userId, $comment);
    }

    /**
     * {@inheritdoc}
     */
    public function publish(NodeContext $node, $version, $language, $userId, $comment = null)
    {
        $event = new PublishNodeContextEvent($node, $language, $version);
        if ($this->eventDispatcher->dispatch(TreeEvents::BEFORE_PUBLISH_NODE_CONTEXT, $event)->isPropagationStopped()) {
            return;
        }


        $state = $this->nodeStateManager->findOneBy(array('node' => $node->getNode(), 'language' => $language));
        if (!$state) {
            $state = new NodeState();
            $state
                ->setNode($node->getNode());
        }

        $state
            ->setVersion($version)
            ->setLanguage($language)
            ->setHash($this->nodeHasher->hashNode($node, $version, $language))
            ->setPublishedAt(new \DateTime())
            ->setPublishUserId($userId);

        $this->nodeStateManager->updateState($state);

        $event = new PublishNodeContextEvent($node, $language, $version);
        if ($this->eventDispatcher->dispatch(TreeEvents::PUBLISH_NODE_CONTEXT, $event)->isPropagationStopped()) {
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOffline(NodeContext $node, $language, $userId, $comment = null)
    {
        $event = new SetNodeOfflineContextEvent($node, $language);
        if ($this->eventDispatcher->dispatch(TreeEvents::BEFORE_SET_NODE_OFFLINE_CONTEXT, $event)->isPropagationStopped()) {
            return;
        }

        $state = $this->nodeStateManager->findOneBy(array('node' => $node->getNode(), 'language' => $language));
        if ($state) {
            $this->nodeStateManager->deleteState($state);
        }

        $event = new SetNodeOfflineContextEvent($node, $language);
        if ($this->eventDispatcher->dispatch(TreeEvents::SET_NODE_OFFLINE_CONTEXT, $event)->isPropagationStopped()) {
            return;
        }
    }

    /*
    protected function deleteCheck(Makeweb_Elements_Tree_Node $node, array $rightsIdentifiers)
    {
        $eid = $node->getEid();
        $uid = MWF_Env::getUid();

        $container = MWF_Registry::getContainer();

        $contentRightsManager = $container->contentRightsManager;

        if (!MWF_Env::getUser()->isGranted(MWF_Core_Acl_Acl::RESOURCE_SUPERADMIN) &&
            !MWF_Env::getUser()->isGranted(MWF_Core_Acl_Acl::RESOURCE_DEVELOPMENT)
        ) {
            $contentRightsManager->calculateRights('internal', $node, $rightsIdentifiers);

            if (true !== $contentRightsManager->hasRight('DELETE', '_all_')) {
                $msg = 'You don\t have the delete right for TID "' . $node->getId() . '"';
                throw new Makeweb_Elements_Tree_Exception($msg);
            }
        }

        $lockIdentifier = new Makeweb_Elements_Element_Identifier($eid);
        $locksService = $container->get('phlexible_element.lock.service');
        $locksRepository = $container->get('phlexible_lock.repository');

        if ($locksService->isLockedPartByOtherUser($lockIdentifier, false, $uid)) {
            $lockInfo = current($locksRepository->findByIdentifierPartAndOtherUid($lockIdentifier, $uid));
            $user = MWF_Core_Users_User_Peer::getByUserID($lockInfo->lockUid);
            $msg = 'Can\'t delete, element is locked by "' . $user->getUsername() . '".';
            throw new Makeweb_Elements_Tree_Exception_LockException($msg);
        }

        foreach ($node->getChildren() as $childNode) {
            $this->deleteCheck($childNode, $rightsIdentifiers);
        }
    }
    */

    /**
     * @param NodeContext $node
     * @param string      $userId
     * @param string      $comment
     */
    private function doDelete(NodeContext $node, $userId, $comment = null)
    {
        foreach ($this->getChildren($node) as $childNode) {
            $this->doDelete($childNode, $userId, $comment);
        }

        $event = new NodeContextEvent($node);
        if ($this->eventDispatcher->dispatch(TreeEvents::BEFORE_DELETE_NODE_CONTEXT, $event)->isPropagationStopped()) {
            return;
        }

        $this->nodeManager->deleteNode($node->getNode());

        $event = new NodeContextEvent($node);
        $this->eventDispatcher->dispatch(TreeEvents::DELETE_NODE_CONTEXT, $event);

        // TODO: -> elements, listener
        /*
        $queueManager = $container->queueManager;
        $job = new Makeweb_Elements_Job_UpdateUsage();
        $job->setEid($eid);
        $queueManager->addUniqueJob($job);
        */
    }
}
