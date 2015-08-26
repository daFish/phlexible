<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Tree;

use Phlexible\Bundle\TreeBundle\Event\MoveNodeContextEvent;
use Phlexible\Bundle\TreeBundle\Event\NodeContextEvent;
use Phlexible\Bundle\TreeBundle\Event\ReorderChildNodesContextEvent;
use Phlexible\Bundle\TreeBundle\Event\ReorderNodeContextEvent;
use Phlexible\Bundle\TreeBundle\Exception\InvalidNodeMoveException;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Bundle\TreeBundle\Node\NodeContextFactoryInterface;
use Phlexible\Bundle\TreeBundle\Node\NodeHasherInterface;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Phlexible\Component\Node\Event\NodeEvent;
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
     * @var TreeContextInterface
     */
    private $treeContext;

    /**
     * @var string
     */
    private $siterootId;

    /**
     * @var NodeManagerInterface
     */
    private $nodeManager;

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
     * @var self
     */
    private $liveTree;

    /**
     * @param TreeContextInterface        $treeContext
     * @param string                      $siterootId
     * @param NodeManagerInterface        $nodeManager
     * @param NodeContextFactoryInterface $nodeContextFactory
     * @param NodeHasherInterface         $nodeHasher
     * @param EventDispatcherInterface    $eventDispatcher
     */
    public function __construct(
        TreeContextInterface $treeContext,
        $siterootId,
        NodeManagerInterface $nodeManager,
        NodeContextFactoryInterface $nodeContextFactory,
        NodeHasherInterface $nodeHasher,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->treeContext = $treeContext;
        $this->siterootId = $siterootId;
        $this->nodeManager = $nodeManager;
        $this->nodeContextFactory = $nodeContextFactory;
        $this->nodeHasher = $nodeHasher;
        $this->eventDispatcher = $eventDispatcher;

        if ($treeContext->getWorkspace() !== 'live') {
            $this->liveTree = new self(
                new LiveTreeContext($treeContext->getLocale()),
                $siterootId,
                $nodeManager,
                $nodeContextFactory,
                $nodeHasher,
                $eventDispatcher
            );
        }
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
     * {@inheritdoc}
     */
    public function getTreeContext()
    {
        return $this->treeContext;
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
                array(
                    'workspace' => $this->treeContext->getWorkspace(),
                    'siterootId' => $this->siterootId,
                    'parentId' => null
                )
            );

            if ($node) {
                $this->nodes['root'] = $this->nodeContextFactory->factory($this, $node, $this->treeContext->getLocale());
            } else {
                $this->nodes['root'] = null;
            }
        }

        if ($this->nodes['root'] === null && $this->liveTree) {
            return $this->liveTree->getRoot();
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
        if (!isset($this->nodes[$id])) {
            $node = $this->nodeManager->findOneBy(array(
                'siterootId' => $this->siterootId,
                'id'         => $id,
                'locale'     => $this->treeContext->getLocale(),
                'workspace'  => $this->treeContext->getWorkspace()
            ));

            if ($node) {
                $this->nodes[$id] = $this->nodeContextFactory->factory($this, $node, $this->treeContext->getLocale());
            } else {
                $this->nodes[$id] = null;
            }
        }

        if ($this->nodes[$id] === null && $this->liveTree) {
            return $this->liveTree->get($id);
        }

        return $this->nodes[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function getWorking($id)
    {
        $node = $this->get('id');

        if (!$node) {
            return null;
        }

        if ($this->treeContext->getWorkspace() === 'live') {
            throw new \Exception("Not capable of fetching work node");
        }

        if ($node->getWorkspace() === 'live') {
            $workingNode = clone $node->getNode();
            $workingNode->setWorkspace('working');
            $this->nodes[$id] = $this->nodeContextFactory->factory($this, $workingNode, $this->treeContext->getLocale());
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
        if ($this->liveTree) {
            $children = $this->liveTree->getChildren($node, $types);

            foreach ($children as $index => $liveNode) {
                $node = $this->get($liveNode->getId());
                if ($node) {
                    $children[$index] = $node;
                }
            }

            return $children;
        }

        $nodes = $this->nodeManager->findByNodeType(
            $types,
            array(
                'siterootId' => $this->siterootId,
                'parentId'   => $node->getId(),
                'locale'     => $this->treeContext->getLocale(),
                'workspace'  => $this->treeContext->getWorkspace()
            ),
            array('sort' => 'ASC')
        );

        $children = array();
        foreach ($nodes as $node) {
            if (!isset($this->nodes[$node->getId()])) {
                $this->nodes[$node->getId()] = $children[] = $this->nodeContextFactory->factory($this, $node, $this->treeContext->getLocale());
            }
        }

        return $children;
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
        if ($node->getNode()->getParentId() === null) {
            return null;
        }

        return $this->get($node->getNode()->getParentId());
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
        return $childNode->getPath() !== $parentNode->getPath() &&
            strpos($childNode->getPath(), $parentNode->getPath()) === 0;
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
        // TODO: check for instance node, interface?
        return false; //$this->nodeManager->isInstance($node->getNode());
    }

    /**
     * {@inheritdoc}
     */
    public function isInstanceMaster(NodeContext $node)
    {
        // TODO: check for instance master, interface?
        return false; //$node->getAttribute('instanceMaster', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedLanguages(NodeContext $node)
    {
        $locales = array();
        foreach ($this->nodeManager->findBy(array('workspace' => 'live', 'id' => $node->getNode()->getId())) as $node) {
            $locales[] = $node->getLocale();
        }

        return $locales;
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync(NodeContext $node)
    {
        if ($node->getWorkspace() === 'working') {
            return true;
        }

        if ($this->liveTree) {
            $liveNode = $this->liveTree->get($node->getId());

            return $node->getContentVersion() !== $liveNode->getContentVersion();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function updateNode(NodeContext $node)
    {
        if ($node->getWorkspace() !== 'working') {
            throw new \Exception("Can only update working node");
        }

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
            ->setPath($parentNode->getPath() . '/' . 'xxx')
            ->setParentPath($parentNode->getPath())
            ->setParentId($parentNode->getId())
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
    public function publish(NodeContext $node, $version, $userId, $comment = null)
    {
        $liveNode = $this->nodeManager->findOneBy(array('workspace' => 'live', 'id' => $node->getNode()->getId(), 'locale' => $this->treeContext->getLocale()));
        if (!$liveNode) {
            $classname = get_class($node->getNode());
            $liveNode = new $classname();
        }

        $liveNode
            ->setContentVersion($version)
            ->setLocale($this->treeContext->getLocale())
            ->setHash($this->nodeHasher->hashNode($node, $version, $this->treeContext->getLocale()))
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($userId);

        $event = new NodeEvent($liveNode);
        if ($this->eventDispatcher->dispatch(TreeEvents::BEFORE_PUBLISH_NODE_CONTEXT, $event)->isPropagationStopped()) {
            return;
        }

        $this->nodeManager->updateState($liveNode);

        $event = new NodeEvent($liveNode);
        if ($this->eventDispatcher->dispatch(TreeEvents::PUBLISH_NODE_CONTEXT, $event)->isPropagationStopped()) {
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOffline(NodeContext $node, $userId, $comment = null)
    {
        $liveNode = $this->nodeManager->findOneBy(array('workspace' => 'live', 'id' => $node->getNode()->getId(), 'locale' => $this->treeContext->getLocale()));
        if ($liveNode) {
            $event = new NodeEvent($liveNode);
            if ($this->eventDispatcher->dispatch(TreeEvents::BEFORE_SET_NODE_OFFLINE_CONTEXT, $event)->isPropagationStopped()) {
                return;
            }

            $this->nodeManager->deleteNode($liveNode);
        }

        $event = new NodeEvent($liveNode);
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

        foreach ($this->nodeManager->findBy(array('id' => $node->getId())) as $deleteNode) {
            $this->nodeManager->deleteNode($deleteNode);
        }

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
