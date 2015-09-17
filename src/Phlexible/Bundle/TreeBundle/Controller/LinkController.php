<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Controller;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Link controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Marcus Stöhr <mstoehr@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 * @Route("/tree/link")
 */
class LinkController extends Controller
{
    const MODE_NOET_NOTARGET = 1;
    const MODE_NOET_TARGET = 2;
    const MODE_ET_NOTARGET = 3;
    const MODE_ET_TARGET = 4;

    /**
     * Return the Element data tree.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="tree_link")
     */
    public function linkAction(Request $request)
    {
        $currentSiterootId = $request->get('siteroot_id');
        $id = $request->get('node', 'root');
        $language = $request->get('language');
        $recursive = (bool) $request->get('recursive');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $iconResolver = $this->get('phlexible_tree.icon_resolver');

        if (null === $language) {
            if ($id !== 'root') {
                $tree = $treeManager->getByNodeId($id);
                $node = $tree->get($id);
            } else {
                $tree = $treeManager->getBySiteRootId($currentSiterootId);
                $node = $tree->getRoot();
            }
            $element = $elementService->findElement($node->getContentId());
            $language = $element->getMasterLanguage();
        }

        if ($id === 'root') {
            $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');
            $siteroots = $siterootManager->findAll();

            // move current siteroot to the beginning
            if ($currentSiterootId !== null) {
                foreach ($siteroots as $index => $siteroot) {
                    if ($siteroot->getId() === $currentSiterootId) {
                        array_unshift($siteroots, $siteroots[$index]);
                        unset($siteroots[$index]);
                    }
                }
            }

            $data = array();
            foreach ($siteroots as $siteroot) {
                $siterootId = $siteroot->getId();
                $tree = $treeManager->getBySiteRootID($siterootId);
                $rootNode = $tree->getRoot();

                $element = $elementService->findElement($rootNode->getContentId());

                $data[] = array(
                    'id' => $rootNode->getId(),
                    'eid' => (int) $rootNode->getContentId(),
                    'text' => $siteroot->getTitle(),
                    'icon' => $iconResolver->resolveNode($rootNode),
                    // 'cls'      => 'siteroot-node',
                    // 'children' => $startNode->hasChildren() ? $this->_recurseNodes($startNode->getChildren(), $language) : array(),
                    'leaf' => !$tree->hasChildren($rootNode),
                    'expanded' => $siterootId === $currentSiterootId,
                );
            }
        } else {
            $tree = $treeManager->getByNodeID($id);
            $node = $tree->get($id);
            $nodes = $tree->getChildren($node);
            $data = $this->recurseLinkNodes($nodes, $language, $recursive);
        }

        return new JsonResponse($data);
    }

    /**
     * Return the Element data tree.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/internal", name="tree_link_internal")
     */
    public function linkInternalAction(Request $request)
    {
        $siterootId = $request->get('siteroot_id');
        $id = $request->get('node', 'root');
        $language = $request->get('language');
        $targetTid = $request->get('value');
        $elementtypeIds = $request->get('element_type_ids');

        if ($elementtypeIds) {
            $elementtypeIds = explode(',', $elementtypeIds);
        } else {
            $elementtypeIds = array();
        }

        $treeManager = $this->get('phlexible_tree.node_manager');
        $elementService = $this->get('phlexible_element.element_service');

        if (!$language) {
            if ($id !== 'root') {
                $tree = $treeManager->getByNodeId($id);
                $node = $tree->get($id);
            } else {
                if (!$siterootId) {
                    return new JsonResponse();
                }
                $tree = $treeManager->getBySiteRootId($siterootId);
                $node = $tree->getRoot();
            }

            $element = $elementService->findElement($node->getTypeId());
            $language = $element->getMasterLanguage();
        }

        $tree = $treeManager->getBySiteRootID($siterootId);
        if ($id === 'root') {
            $startNode = $tree->getRoot();
        } else {
            $startNode = $tree->get($id);
        }

        $targetNode = null;
        if ($targetTid) {
            $targetTree = $treeManager->getByNodeId($targetTid);
            $targetNode = $targetTree->get($targetTid);
        }

        if (!count($elementtypeIds)) {
            $mode = !$targetTid ? self::MODE_NOET_NOTARGET : self::MODE_NOET_TARGET;

            if ($id === 'root') {
                $nodes = array($startNode);
            } else {
                $nodes = $tree->getChildren($startNode);
            }
            $data = $this->recurseLinkNodes($nodes, $language, $mode, $targetNode);
        } else {
            $mode = !$targetTid ? self::MODE_ET_NOTARGET : self::MODE_ET_TARGET;

            $data = $this->findLinkNodes($startNode->getTree()->getSiterootId(), $language, $elementtypeIds);

            if ($elementtypeIds) {
                $data = $this->recursiveTreeStrip($data);
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/intrasiteroot", name="tree_link_intrasiteroot")
     */
    public function linkIntrasiterootAction(Request $request)
    {
        $siterootId = $request->get('siteroot_id');
        $id = $request->get('node', 'root');
        $recursive = (bool) $request->get('recursive', false);
        $language = $request->get('language');
        $elementtypeIds = $request->get('element_type_ids', array());
        $targetTid = $request->get('value');

        $treeManager = $this->get('phlexible_tree.node_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $iconResolver = $this->get('phlexible_tree.icon_resolver');

        // TODO: switch to master language of element
        $defaultLanguage = $this->container->getParameter('phlexible_cms.languages.default');

        if (!$language) {
            $language = $defaultLanguage;
        }

        if ($elementtypeIds) {
            $elementtypeIds = explode(',', $elementtypeIds);
        } else {
            $elementtypeIds = array();
        }

        $targetTree = null;
        $targetNode = null;
        if ($targetTid) {
            $targetTree = $treeManager->getByNodeID($targetTid);
            $targetNode = $targetTree->get($targetTid);
        }

        if ($id === 'root') {
            $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');
            $siteroots = $siterootManager->findAll();

            if ($siterootId) {
                foreach ($siteroots as $index => $siteroot) {
                    if ($siteroot->getId() === $siterootId) {
                        unset($siteroots[$index]);
                        break;
                    }
                }
            }

            $data = array();
            foreach ($siteroots as $siteroot) {
                $tree = $treeManager->getBySiteRootID($siteroot->getId());
                $rootNode = $tree->getRoot();

                $element = $elementService->findElement($rootNode->getTypeId());
                $elementVersion = $elementService->findElementVersion($element, $element->getLatestVersion());

                $children = false;
                if ($targetTree && $siteroot->getId() === $targetTree->getSiterootId()) {
                    if (!count($elementtypeIds)) {
                        $mode = !$targetTid ? self::MODE_NOET_NOTARGET : self::MODE_NOET_TARGET;

                        $nodes = $tree->getChildren($rootNode);
                        $children = $this->recurseLinkNodes($nodes, $language, $mode, $targetNode);
                    } else {
                        $mode = !$targetTid ? self::MODE_ET_NOTARGET : self::MODE_ET_TARGET;

                        $children = $this->findLinkNodes($tree->getSiterootId(), $language, $elementtypeIds);

                        if ($elementtypeIds) {
                            $children = $this->recursiveTreeStrip($children);
                        }
                    }
                }

                $data[] = array(
                    'id' => $rootNode->getID(),
                    'eid' => $rootNode->getTypeId(),
                    'text' => $siteroot->getTitle(),
                    'icon' => $iconResolver->resolveNode($rootNode),
                    'children' => $children,
                    'leaf' => !$tree->hasChildren($rootNode),
                    'expanded' => false,
                );
            }
        } else {
            $tree = $treeManager->getByNodeID($id);
            $startNode = $tree->get($id);

            if (!count($elementtypeIds)) {
                $mode = !$targetTid ? self::MODE_NOET_NOTARGET : self::MODE_NOET_TARGET;

                $nodes = $tree->getChildren($startNode);
                $data = $this->recurseLinkNodes($nodes, $language, $mode, $targetNode);
            } else {
                $mode = !$targetTid ? self::MODE_ET_NOTARGET : self::MODE_ET_TARGET;

                $data = $this->findLinkNodes($tree->getSiterootId(), $language, $elementtypeIds);

                if ($elementtypeIds) {
                    $data = $this->recursiveTreeStrip($data);
                }
            }

            //$nodes = $startNode->getChildren();
            //$data = $this->_recurseLinkNodes($nodes, $language, $mode);
        }

        return new JsonResponse($data);
    }

    /**
     * @param string $siteRootId
     * @param string $language
     * @param array  $elementtypeIds
     *
     * @return array
     */
    private function findLinkNodes($siteRootId, $language, array $elementtypeIds)
    {
        $treeManager = $this->get('phlexible_tree.node_manager');
        $elementService = $this->get('phlexible_element.element_service');

        $iconResolver = $this->get('phlexible_tree.icon_resolver');

        $select = $db->select()
            ->distinct()
            ->from(array('et' => $db->prefix.'element_tree'), array('id'))
            ->join(array('e' => $db->prefix.'element'), 'et.eid = e.eid', array())
            ->where('et.siteroot_id = ?', $siteRootId)
            ->where('e.element_type_id IN (?)', $elementtypeIds)
            ->order('et.sort');

        $treeIds = $db->fetchCol($select);

        $data = array();

        $rootTreeId = null;

        foreach ($treeIds as $treeId) {
            $tree = $treeManager->getByNodeId($treeId);
            $node = $tree->get($treeId);

            $element = $elementService->findelement($node->getTypeId());
            $elementVersion = $elementService->findElementVersion($element, $element->getLatestVersion());

            if (!isset($data[$treeId])) {
                $data[$node->getId()] = array(
                    'id' => $node->getId(),
                    'eid' => $node->getTypeId(),
                    'text' => $elementVersion->getBackendTitle($language, $element->getMasterLanguage()).' ['.$node->getId().']',
                    'icon' => $iconResolver->resolveElement($element),
                    'children' => array(),
                    'leaf' => true,
                    'expanded' => false,
                    'disabled' => !in_array($elementVersion->getElementTypeID(), $elementtypeIds),
                );
            }

            do {
                $parentNode = $tree->getParent($node);

                if (!$parentNode) {
                    $rootTreeId = $node->getId();
                    break;
                }

                if (!isset($data[$parentNode->getId()])) {
                    $element = $elementService->findElement($parentNode->getTypeId());
                    $elementVersion = $elementService->findElementVersion($element, $element->getLatestVersion());

                    $data[$parentNode->getId()] = array(
                        'id' => $parentNode->getId(),
                        'eid' => $parentNode->getTypeId(),
                        'text' => $elementVersion->getBackendTitle($language, $element->getMasterLanguage()).' ['.$parentNode->getId().']',
                        'icon' => $iconResolver->resolveNode($parentNode),
                        'children' => array(),
                        'leaf' => false,
                        'expanded' => false,
                        'disabled' => !in_array($elementVersion->getElementTypeID(), $elementtypeIds),
                    );
                } else {
                    $data[$parentNode->getId()]['leaf'] = false;
                }

                $data[$parentNode->getId()]['children'][$node->getId()] = &$data[$node->getId()];

                $node = $parentNode;
            } while ($parentNode);
        }

        if (!count($data)) {
            return array();
        }

        $data = $this->stripLinkNodeKeys($data[$rootTreeId], $db);

        return $data['children'];
    }

    /**
     * @param array      $data
     * @param Connection $connection
     *
     * @return array
     */
    private function stripLinkNodeKeys($data, Connection $connection)
    {
        if (is_array($data['children']) && count($data['children'])) {
            $sortSelect = $db->select()
                ->from($db->prefix.'element_tree', array('id', 'sort'))
                ->where('parent_id = ?', $data['id'])
                ->where('id IN (?)', array_keys($data['children']))
                ->order('sort');

            $sortTids = $db->fetchPairs($sortSelect);
            $sortedTids = array();
            foreach (array_keys($data['children']) as $tid) {
                $sortedTids[$tid] = $sortTids[$tid];
            }

            array_multisort($sortedTids, $data['children']);

            $data['children'] = array_values($data['children']);

            foreach ($data['children'] as $key => $item) {
                $data['children'][$key] = $this->stripLinkNodeKeys($item, $db);
            }
        }

        return $data;
    }

    /**
     * Recurse over tree nodes.
     *
     * @param NodeContext[] $nodes
     * @param string        $language
     * @param int           $mode
     * @param NodeContext   $targetNode
     *
     * @return array
     */
    private function recurseLinkNodes(array $nodes, $language, $mode, NodeContext $targetNode = null)
    {
        $elementService = $this->get('phlexible_element.element_service');
        $iconResolver = $this->get('phlexible_tree.icon_resolver');

        $data = array();

        foreach ($nodes as $node) {
            $element = $elementService->findElement($node->getContentId());
            $elementVersion = $elementService->findElementVersion($element, $element->getLatestVersion());
            $elementtype = $elementService->findElementtype($element);

            $tid = $node->getId();
            $tree = $node->getTree();
            $children = $tree->getChildren($node);

            $dataNode = array(
                'id' => $node->getId(),
                'eid' => $node->getContentId(),
                'text' => $elementVersion->getBackendTitle($language, $element->getMasterLanguage()).' ['.$tid.']',
                'icon' => $iconResolver->resolveNode($node),
                'children' => !$tree->hasChildren($node)
                        ? array()
                        : $mode === self::MODE_NOET_TARGET && $tree->isParentOf($node, $targetNode)
                            ? $this->recurseLinkNodes($children, $language, $mode, $targetNode)
                            : false,
                'leaf' => !$tree->hasChildren($node),
                'expanded' => false,
            );

            /*
            $leafCount = 0;
            if (is_array($dataNode['children']))
            {
                foreach($dataNode['children'] as $child)
                {
                    $leafCount += $child['leafCount'];
                    if (!isset($child['disabled']) || !$child['disabled'])
                    {
                        ++$leafCount;
                    }
                }
            }
            $dataNode['leafCount'] = $leafCount;
            */

            $data[] = $dataNode;
        }

        return $data;
    }

    /**
     * Strip all disabled nodes recursivly.
     *
     * @param array $data
     *
     * @return array
     */
    private function recursiveTreeStrip(array $data)
    {
        if (count($data) === 1 && !empty($data[0]['children'])) {
            return $this->recursiveTreeStrip($data[0]['children']);
        }

        return $data;
    }
}
