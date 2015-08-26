<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\TreeBundle\Controller\Tree\NodeSaver;
use Phlexible\Component\Tree\WorkingTreeContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tree controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Marcus Stöhr <mstoehr@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 * @Route("/tree/tree")
 */
class TreeController extends Controller
{
    const MODE_NOET_NOTARGET = 1;
    const MODE_NOET_TARGET = 2;
    const MODE_ET_NOTARGET = 3;
    const MODE_ET_TARGET = 4;

    /**
     * Return the Element data tree
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/nodes", name="tree_nodes")
     */
    public function treeAction(Request $request)
    {
        $siterootId = $request->get('siterootId');
        $tid = $request->get('node');
        $language = $request->get('language');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $nodeSerializer = $this->get('phlexible_tree.node_serializer');

        // TODO: switch to master language of element
        $defaultLanguage = $this->container->getParameter('phlexible_cms.languages.default');

        $treeContext = new WorkingTreeContext($language);
        $tree = $treeManager->getBySiteRootId($treeContext, $siterootId);
        $rootNode = $tree->getRoot();

        $data = array();
        if ($rootNode) {
            if ($tid === null || $tid < 1) {
                $data = array($nodeSerializer->serializeNode($rootNode, $language));
            } else {
                $node = $tree->get($tid);

                // check if children of this node should be shown
                $element = $elementService->findElement($node->getContentId());
                $elementtype = $elementService->findElementtype($element);

                $nodes = $tree->getChildren($node);
                if (!empty($nodes) && !$elementtype->getHideChildren()) {
                    $data = $nodeSerializer->serializeNodes($nodes, $language);
                }
            }
        }

        return new JsonResponse($data);
    }

    /**
     * List all types
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/types", name="tree_node_types")
     */
    public function typesAction(Request $request)
    {
        $nodeId = $request->get('node');
        $language = $request->get('language');

        $nodeTypeManager = $this->get('phlexible_tree.node_type_manager');
        $treeManager = $this->get('phlexible_tree.tree_manager');

        $treeContext = new WorkingTreeContext($language);
        $tree = $treeManager->getByNodeId($treeContext, $nodeId);
        $node = $tree->get($nodeId);

        $types = array();
        foreach ($nodeTypeManager->getTypesForNode($node) as $name => $type) {
            $types[$name] = array(
                'name' => $name,
                'icon' => '',
                'type' => $type,
            );
        }

        ksort($types);
        $types = array_values($types);

        return new JsonResponse($types);
    }

    /**
     * Create an Element
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="tree_create")
     */
    public function createAction(Request $request)
    {
        $parentId = $request->get('id');
        $siterootId = $request->get('siterootId');
        $type = $request->get('type');
        $afterId = $request->get('prevId');
        $sortMode = $request->get('sort', 'title');
        $sortDir = $request->get('sortDir', 'asc');
        $navigation = $request->get('navigation') ? true : false;
        $restricted = $request->get('restricted') ? true : false;
        $language = $request->get('language');
        $masterLanguage = $request->get('masterlanguage');

        $treeManager = $this->get('phlexible_tree.tree_manager');

        $treeContext = new WorkingTreeContext($language);
        $tree = $treeManager->getBySiteRootId($treeContext, $siterootId);
        $parentNode = $tree->get($parentId);
        $afterNode = $tree->get($afterId);

        $userId = $this->getUser()->getId();

        $elementSource = $elementService->findElementSource($elementtypeId);

        $elementVersion = $elementService->createElement($elementSource, $masterLanguage, $userId);
        $element = $elementVersion->getElement();

        $node = $tree->createNode(
            $parentNode,
            $afterNode,
            $elementVersion,
            array(),
            $this->getUser()->getId(),
            $sortMode,
            $sortDir,
            $navigation,
            $restricted
        );

        return new ResultResponse(
            true,
            'Element EID "' . $element->getEid() . ' (' . $masterLanguage . ')" created.',
            array(
                'eid'             => $element->getEid(),
                'tid'             => $node->getId(),
                'master_language' => $masterLanguage,
                'navigation'      => $navigation,
                'restricted'      => $restricted
            )
        );
    }

    /**
     * Create an Element
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/createinstance", name="tree_create_instance")
     */
    public function createInstanceAction(Request $request)
    {
        $parentId = $request->get('id');
        $afterId = $request->get('prev_id');
        $sourceId = $request->get('for_tree_id');

        $treeManager = $this->get('phlexible_tree.tree_manager');

        $tree = $treeManager->getByNodeId($parentId);

        $tree->createInstance($parentId, $afterId, $sourceId, $this->getUser()->getId());

        return new ResultResponse(true, 'Instance created.');
    }

    /**
     * Copy an Element
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/copy", name="tree_copy")
     */
    public function copyAction(Request $request)
    {
        $parentId = $request->get('id');
        $sourceId = $request->get('for_tree_id');
        $prevId = $request->get('prev_id');

        $elementService = $this->get('phlexible_element.element_service');
        $treeManager = $this->get('phlexible_tree.tree_manager');

        $tree = $treeManager->getByNodeId($sourceId);
        $sourceNode = $tree->get($sourceId);
        $sourceEid = $sourceNode->getContentId();

        $select = $db->select()
            ->from($db->prefix . 'element', array('element_type_id', 'masterlanguage'))
            ->where('eid = ?', $sourceEid);

        $sourceElementRow = $db->fetchRow($select);

        $targetElement = $elementManager->create(
            $sourceElementRow['element_type_id'],
            false,
            $sourceElementRow['masterlanguage']
        );
        $targetEid = $targetElement->getEid();

        // place new element in element_tree
        $targetId = $tree->add($parentId, $targetEid, $prevId);

        // copy element version data
        $sourceElement = $elementManager->getByEid($sourceEid);
        $sourceElementVersion = $sourceElement->getLatestVersion();
        $targetElementVersion = $sourceElementVersion->copy($targetEid);

        // copy tree node settings
        $tree->copyPage(
            $sourceId,
            $targetElementVersion->getVersion(),
            $sourceElementVersion->getVersion(),
            $targetId
        );

        return new ResultResponse(true, 'Element copied.', array('id' => $targetId));
    }

    /**
     * Move an Element
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/move", name="tree_move")
     */
    public function moveAction(Request $request)
    {
        $id = $request->get('id');
        $targetId = $request->get('target');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $tree = $treeManager->getByNodeId($id);
        $node = $tree->get($id);

        $targetTree = $treeManager->getByNodeId($targetId);
        $targetNode = $tree->get($targetId);

        if ($id === $targetId) {
            return new ResultResponse(false, 'source_id === target_id');
        }

        $tree->move($node, $targetNode, $this->getUser()->getId());

        return new ResultResponse(true, 'Element moved.', array('id' => $id, 'parent_id' => $targetId));
    }

    /**
     * predelete action
     * check if element has instances
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/predelete", name="tree_delete_check")
     */
    public function checkDeleteAction(Request $request)
    {
        $treeId = $request->get('id');
        $language = $request->get('language', 'de');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $treeMeditator = $this->get('phlexible_tree.mediator');
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $nodeId = $treeId[0];
        $tree = $treeManager->getByNodeId($nodeId);
        $node = $tree->get($nodeId);

        $instances = $treeManager->getInstanceNodes($node);

        if (count($instances) > 1) {
            $instancesArray = array();
            foreach ($instances as $instanceNode) {
                $siteroot = $siterootManager->find($instanceNode->getTree()->getSiterootId());
                $instanceTitle = $treeMeditator->getField($instanceNode, 'backend', $language);

                $instancesArray[] = array(
                    $instanceNode->getId(),
                    $siteroot->getTitle(),
                    $instanceTitle,
                    $instanceNode->getCreatedAt()->format('Y-m-d H:i:s'),
                    (bool) $instanceNode->getTree()->isInstanceMaster($instanceNode),
                    (bool) ($instanceNode->getId() === $nodeId)
                );
            }

            return new ResultResponse(true, '', $instancesArray);
        }

        return new ResultResponse(true, '', array());
    }

    /**
     * Delete an Element
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/delete", name="tree_delete")
     */
    public function deleteAction(Request $request)
    {
        $treeIds = $request->get('id');
        if (!is_array($treeIds)) {
            $treeIds = array($treeIds);
        }

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $elementService = $this->get('phlexible_element.element_service');

        foreach ($treeIds as $treeId) {
            $tree = $treeManager->getByNodeId($treeId);
            $node = $tree->get($treeId);
            $element = $elementService->findElement($node->getContentId());

            $elementService->deleteElement($element);
            $tree->delete($node, $this->getUser()->getId());
        }

        return new ResultResponse(true, 'Item(s) deleted');
    }

    /**
     * Save node
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="tree_save")
     */
    public function saveAction(Request $request)
    {
        $language = $request->get('language');

        $iconResolver = $this->get('phlexible_tree.icon_resolver');
        $dataSaver = new NodeSaver(
            $this->get('event_dispatcher'),
            $this->container->getParameter('phlexible_cms.languages.available')
        );

        $nodeId = $request->get('id');
        $treeManager = $this->get('phlexible_tree.tree_manager');
        $treeContext = new WorkingTreeContext($language);
        $tree = $treeManager->getByNodeId($treeContext, $nodeId);
        $node = $tree->getWorking($nodeId);

        $dataSaver->save($node, $request, $this->getUser());

        $icon = $iconResolver->resolveNode($node);

        $msg = "Node {$node->getId()} updated.";

        $data = array(
            'title'         => $node->getField('backend', $language),
            'icon'          => $icon,
            'navigation'    => $node->getInNavigation(),
            'restricted'    => $node->getAttribute('needAuthentication'),
        );

        return new ResultResponse(true, $msg, $data);
    }
}
