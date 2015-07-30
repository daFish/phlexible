<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\TreeBundle\Doctrine\TreeFilter;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * List controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/tree/list")
 */
class ListController extends Controller
{
    /**
     * List all Elements
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="tree_list")
     */
    public function listAction(Request $request)
    {
        $start = $request->get('start', 0);
        $limit = $request->get('limit', 25);
        $sort = $request->get('sort');
        $dir = $request->get('dir');
        $tid = $request->get('tid');
        $language = $request->get('language');
        $filterValues = $request->get('filter');
        if ($filterValues) {
            $filterValues = json_decode($filterValues, true);
        } else {
            $filterValues = array();
        }

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $nodeSerializer = $this->get('phlexible_tree.node_serializer');

        $tree = $treeManager->getByNodeID($tid);
        $node = $tree->get($tid);
        $eid = $node->getContentId();
        $element = $elementService->findElement($eid);
        $elementMasterLanguage = $element->getMasterLanguage();

        if (!$language) {
            $language = $elementMasterLanguage;
        }

        $filter = new TreeFilter(
            $this->get('doctrine.dbal.default_connection'),
            $request->getSession(),
            $this->get('event_dispatcher'),
            $node->getId(),
            $language
        );

        $filter
            ->setFilterValues($filterValues)
            ->setSortMode($sort)
            ->setSortDir($dir);

        $childIds = $filter->getIds($limit, $start);
        $cnt = $filter->getCount();

        $data = array();
        foreach ($childIds as $childId => $latestVersion) {
            $childNode = $tree->get($childId);

            $data[] = $nodeSerializer->serializeNode($childNode, $language);
        }

        return new JsonResponse(array(
            'list'  => $data,
            'total' => $cnt
        ));
    }

    /**
     * Node reordering
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/sort", name="tree_list_sort")
     */
    public function sortAction(Request $request)
    {
        $tid = $request->get('tid');
        $mode = $request->get('mode');
        $dir = strtolower($request->get('dir'));
        $sortTids = $request->get('sort_ids');
        $sortTids = json_decode($sortTids, true);

        $treeManager = $this->get('phlexible_tree.node_manager');
        $nodeSorter = $this->get('phlexible_tree.node_sorter');

        $tree = $treeManager->getByNodeId($tid);
        $node = $tree->get($tid);

        $node->setSortMode($mode);
        $node->setSortDir($dir);

        if ($mode !== TreeInterface::SORT_MODE_FREE) {
            $sortTids = $nodeSorter->sort($node);
        }

        if (count($sortTids)) {
            $tree->reorderChildren($node, $sortTids);
        }

        return new ResultResponse(true, 'Tree sort published.');
    }
}
