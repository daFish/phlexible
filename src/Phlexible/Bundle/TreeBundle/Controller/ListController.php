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

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\TreeBundle\Doctrine\TreeFilter;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Component\AccessControl\Model\DomainObjectInterface;
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

        $data = array();

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $iconResolver = $this->get('phlexible_element.icon_resolver');
        $permissionRegistry = $this->get('phlexible_access_control.permission_registry');

        $tree = $treeManager->getByNodeID($tid);
        $node = $tree->get($tid);
        $eid = $node->getTypeId();
        $element = $elementService->findElement($eid);
        $elementMasterLanguage = $element->getMasterLanguage();
        $elementVersion = $elementService->findLatestElementVersion($element);
        $elementtype = $elementService->findElementtype($element);
        //$elementData = $element->getData(0, 'en');

        if (!$language) {
            $language = $elementMasterLanguage;
        }

        $userRights = array();
        $userAdminRights = null;
        if ($node instanceof DomainObjectInterface) {
            if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
                //$contentRightsManager->calculateRights('internal', $rightsNode, $rightsIdentifiers);

                if (!$this->isGranted(['permission' => 'VIEW', 'language' => $language], $node)) {
                    return new JsonResponse([
                        'parent' => null,
                        'list'   => array(),
                        'total'  => 0
                    ]);
                }

                // TODO: fix
                $userRights = array();
                foreach ($permissionRegistry->get(get_class($node))->all() as $permission) {
                    $userRights[] = $permission->getName();
                }
            } else {
                $userRights = array();
                foreach ($permissionRegistry->get(get_class($node))->all() as $permission) {
                    $userRights[] = $permission->getName();
                }
            }
        }

        $parent = array(
            'tid'             => (int) $tid,
            'teaser_id'       => (int) 0,
            'eid'             => (int) $eid,
            'title'           => $elementVersion->getBackendTitle($language, $elementMasterLanguage),
            'element_type_id' => $elementtype->getId(),
            'element_type'    => $elementtype->getTitle(),
            'icon'            => $iconResolver->resolveTreeNode($node, $language),
            'author'          => 'author',
            'version'         => $elementVersion->getVersion(),
            'create_time'     => $elementVersion->getCreatedAt()->format('Y-m-d H:i:s'),
            'publish_time'    => $elementVersion->getCreatedAt()->format('Y-m-d H:i:s'), // $node->getPublishDate($language),
            'custom_date'     => $elementVersion->getCustomDate($language) ? $elementVersion->getCustomDate($language)->format('Y-m-d H:i:s') : null,
            'language'        => $language,
            'sort'            => 0,
            'sort_mode'       => $node->getSortMode(),
            'sort_dir'        => $node->getSortDir(),
            'version_latest'  => 1, //(int) $node->getLatestVersion(),
            'version_online'  => 2, //(int) $node->getOnlineVersion($language),
            'status'          => ' o_O ',
            'rights'          => $userRights,
            'qtip'            =>
                $elementtype->getTitle() . ', Version ' . $elementtype->getRevision() . '<br>' .
                'Version ' . $elementVersion->getVersion() . '<br>' .
                37 . ' Versions<br>'
        );

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

            if (!$userAdminRights) {
                //$contentRightsManager->calculateRights('internal', $rightsNode, $rightsIdentifiers);

                if (!$this->isGranted(['permission' => 'VIEW', 'language' => $language], $node)) {
                    continue;
                }

                // TODO: fix
                $userRights = array();
                foreach ($permissionRegistry->get(get_class($node))->all() as $permission) {
                    $userRights[] = $permission->getName();
                }
            } else {
                $userRights = array();
                foreach ($permissionRegistry->get(get_class($node))->all() as $permission) {
                    $userRights[] = $permission->getName();
                }
            }

            $childElement = $elementService->findElement($childNode->getTypeId());
            $childElementVersion = $elementService->findLatestElementVersion($childElement);
            $childTitle = $childElementVersion->getBackendTitle($language, $childElement->getMasterLanguage());
            $childElementtype = $elementService->findElementtype($childElement);

            $data[] = array(
                'tid'             => (int) $childNode->getId(),
                'eid'             => (int) $childElement->getEid(),
                '_type'           => 'element',
                'title'           => $childTitle,
                'element_type_id' => $childElementtype->getId(),
                'element_type'    => $childElementtype->getTitle(),
                'navigation'      => 0, //$childNode->inNavigation($childElementVersion->getVersion()),
                'restricted'      => 0, //$childNode->isRestricted($childElementVersion->getVersion()),
                'icon'            => $iconResolver->resolveTreeNode($childNode, $language),
                'author'          => 'author',
                'version'         => $childElementVersion->getVersion(),
                'create_time'     => $childNode->getCreatedAt()->format('Y-m-d H:i:s'),
                // 'change_time'     => $child['modify_time'],
                'publish_time'    => $childNode->getCreatedAt()->format('Y-m-d H:i:s'),//$childNode->getPublishDate($language),
                'custom_date'     => $childElementVersion->getCustomDate($language) ? $childElementVersion->getCustomDate($language)->format('Y-m-d H:i:s') : null,
                'language'        => $language,
                'sort'            => (int) $childNode->getSort(),
                'version_latest'  => 1, //(int) $childNode->getLatestVersion(),
                'version_online'  => 2, //(int) $childNode->getOnlineVersion($language),
                'status'          => '>o>',
                'rights'          => $userRights,
                'qtip'            => $childElementtype->getTitle() . ', ' .
                    'ET Version ' . $childElementtype->getRevision() . '<br>' .
                    'Version ' . $childElementVersion->getVersion() . '<br>',
            );
        }

        //$data['totalChilds'] = $element->getChildCount();

        return new JsonResponse(array(
            'parent' => $parent,
            'list'   => $data,
            'total'  => $cnt
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

        $treeManager = $this->get('phlexible_tree.tree_manager');
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
