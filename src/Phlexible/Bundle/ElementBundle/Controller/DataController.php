<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\ElementVersion\Diff\Differ;
use Phlexible\Bundle\ElementBundle\ElementVersion\Serializer\ArraySerializer as ElementVersionArraySerializer;
use Phlexible\Bundle\ElementBundle\Event\LoadDataEvent;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\TreeBundle\Doctrine\TreeFilter;
use Phlexible\Bundle\TreeBundle\Entity\NodeLock;
use Phlexible\Bundle\TreeBundle\Entity\PageNode;
use Phlexible\Bundle\TreeBundle\Entity\PartNode;
use Phlexible\Bundle\TreeBundle\Entity\StructureNode;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Elementtype\ElementtypeStructure\Serializer\ArraySerializer as ElementtypeArraySerializer;
use Phlexible\Component\Tree\WorkingTreeContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Data controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/data")
 * @Security("is_granted('ROLE_ELEMENTS')")
 */
class DataController extends Controller
{
    /**
     * @param NodeContext $node
     *
     * @return array
     */
    private function createHistory(NodeContext $node)
    {
        $nodeChangeManager = $this->get('phlexible_tree.node_change_manager');

        // versions
        $publishActions = $nodeChangeManager->findBy(
            array(
                'nodeId' => $node->getId(),
                'action' => 'publishNode'
            )
        );
        $recentOnlineVersions = array();
        foreach ($publishActions as $publishAction) {
            $recentOnlineVersions[$publishAction->getVersion()] = true;
        }

        $versions = array();
        foreach ($node->getContentVersions() as $versionRow) {
            $versions[] = array(
                'version'      => (int) $versionRow,//['version'],
                'format'       => 1,//(int) $versionRow['format'],
                'createdAt'    => date('Y-m-d H:i:s'),//$versionRow['createdAt'],
                'isPublished'  => false, // TODO: $versionRow['version'] === $onlineVersion,
                'wasPublished' => isset($recentOnlineVersions[$versionRow]),//$versionRow['version']]),
            );
        }

        foreach ($publishActions as $publishAction) {
            if (!$publishAction->getVersion()) {
                continue;
            }
            $versions[$publishAction->getVersion()]['isPublished'] = true;
            if ($publishAction->getVersion() === 1) { // TODO: $onlineVersion) {
                $versions[$publishAction->getVersion()]['isPublished'] = true;
            } else {
                $versions[$publishAction->getVersion()]['wasPublished'] = true;
            }
        }

        return array_values($versions);
    }

    /**
     * @param NodeContext $node
     *
     * @return array
     */
    private function createAllowedChildren(NodeContext $node)
    {
        $allowedChildren = array();
        // TODO: switch to type manager
        /*
        foreach ($elementService->findAllowedChildren($elementtype) as $childElementtype) {
            if ($childElementtype->getType() !== 'full') {
                continue;
            }

            $allowedChildren[] = array(
                $childElementtype->getId(),
                $childElementtype->getName(),
                $iconResolver->resolveElementtype($childElementtype),
            );
        }
        */

        return $allowedChildren;
    }

    /**
     * @param NodeContext $node
     *
     * @return array
     */
    private function createConfiguration(NodeContext $node)
    {
        $configuration = $node->getAttributes();
        $configuration['title'] = $node->getTitle();
        $configuration['navigation_title'] = $node->getNavigationTitle();
        $configuration['backend_title'] = $node->getBackendTitle();
        $configuration['slug'] = $node->getSlug();
        $configuration['navigation'] = $node->getInNavigation() ? true : false;

        return $configuration;
    }

    /**
     * @param NodeContext $node
     *
     * @return array
     */
    private function createInstances(NodeContext $node)
    {
        $nodeManager = $this->get('phlexible_tree.node_manager');
        $treeManager = $this->get('phlexible_tree.tree_manager');
        $iconResolver = $this->get('phlexible_tree.icon_resolver');

        $instances = array();
        foreach ($nodeManager->getInstanceNodes($node->getNode()) as $instanceNode) {
            $instanceNodeContext = $treeManager->getByNodeId($node->getTree()->getTreeContext(), $instanceNode->getId())->get($instanceNode->getId());
            $instance = array(
                'id'             => $instanceNode->getId(),
                'instanceMaster' => false,
                'modifiedAt'     => $instanceNode->getCreatedAt()->format('Y-m-d H:i:s'),
                'icon'           => $iconResolver->resolveNode($instanceNodeContext),
                'type'           => 'treenode',
                'link'           => array(),
            );

            if ($instanceNode->getSiterootId() !== $node->getSiterootId()) {
                $instance['link'] = array(
                    'start_tid_path' => '/' . implode('/', $treeManager->getByNodeId($node->getTree()->getTreeContext(), $instanceNode->getId())->getIdPath($instanceNode)),
                );
            }

            $instances[] = $instance;
        }

        return $instances;
    }

    /**
     * @param NodeContext $node
     *
     * @return array
     */
    private function createPaging(NodeContext $node)
    {
        return array();
        $paging = array();
        $parentNode = $node->getTree()->getParent($node);
        if ($parentNode) {
            $parentElement = $elementService->findElement($parentNode->getContentId());
            $parentElementtype = $elementService->findElementtype($parentElement);
            if ($parentElementtype->getHideChildren()) {
                $filter = new TreeFilter(
                    $this->get('doctrine.dbal.default_connection'),
                    $request->getSession(),
                    $this->get('event_dispatcher'),
                    $parentNode->getId(),
                    $language
                );
                $paging = $filter->getPager($node->getId());
            }
        }

        return $paging;
    }

    /**
     * @param NodeContext $node
     *
     * @return array
     */
    private function createPermissions(NodeContext $node)
    {
        $permissions = array();
        $permissionRegistry = $this->get('phlexible_access_control.permission_registry');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            if (!$this->isGranted(array('permission' => 'VIEW', 'language' => $language), $node)) {
                throw new AccessDeniedHttpException();
            }

            // TODO: fix
            foreach ($permissionRegistry->get(get_class($node))->all() as $permission) {
                $permissions[] = $permission->getName();
            }
        } else {
            foreach ($permissionRegistry->get(get_class($node))->all() as $permission) {
                $permissions[] = $permission->getName();
            }
        }

        return $permissions;
    }

    /**
     * @param NodeContext $node
     *
     * @return array
     */
    private function createUrls(NodeContext $node)
    {
        $urls = array(
            'preview' => '',
            'online'  => '',
        );

        if ($node->getNode() instanceof PageNode || $node->getNode() instanceof StructureNode || $node->getNode() instanceof PartNode) {
            $urls['preview'] = $this->generateUrl('cms_preview', array('nodeId' => $node->getId(), '_locale' => $node->getLocale()));
            $urls['online'] = $this->generateUrl($node->getId());
        }

        return $urls;
    }

    /**
     * @param NodeContext $node
     * @param int         $baseVersion
     * @param int         $compareVersion
     *
     * @return array|null
     */
    private function createDiff(NodeContext $node, $baseVersion, $compareVersion = null)
    {
        $elementSerializer = new ElementVersionArraySerializer();

        if (!$compareVersion && $node->getContentVersion() === $baseVersion) {
            return null;
        }

        $diffResult = null;
        if ($compareVersion && $baseVersion !== $compareVersion) {
            $compareElementVersion = $elementService->findElementVersion($element, $compareVersion);
            $serializedCompareValues = $elementSerializer->serialize($compareElementVersion, $language);

            $differ = new Differ();
            $serializedValues = $differ->diff($serializedValues, $serializedCompareValues);
        }

        return array(
            'enabled'        => true,
            'version'        => $version,
            'compareVersion' => $compareVersion,
        );
    }

    /**
     * Load element data
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/content", name="elements_content")
     */
    public function contentAction(Request $request)
    {
        $nodeId = (int) $request->get('id');
        $language = $request->get('language');
        $version = $request->get('version');
        $unlockId = $request->get('unlock');
        $doLock = (bool) $request->get('lock', false);

        if (!$nodeId) {
            throw new BadRequestHttpException('No ID received.');
        }

        if (!$language) {
            throw new BadRequestHttpException('No language received.');
        }

        $diff = $request->get('diff');
        $compareVersion = (int) $request->get('compareVersion');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $nodeLockManager = $this->get('phlexible_tree.node_lock_manager');
        $userManager = $this->get('phlexible_user.user_manager');

        $treeContext = new WorkingTreeContext($language);
        $tree = $treeManager->getByNodeId($treeContext, $nodeId);
        $node = $tree->get($nodeId);

        $element = $elementService->findElement($node->getContentId());

        if (!$version) {
            $version = $element->getLatestVersion();
        }

        $elementVersion = $elementService->findElementVersion($element, $version);

        $elementtype = $elementService->findElementtype($element);
        $elementtypeStructure = $elementtype->getStructure();

        // lock

        if ($unlockId !== null) {
            $unlockNode = $tree->get($unlockId);
            if ($unlockNode && $nodeLockManager->isLockedByUser($unlockNode, $this->getUser()->getId())) {
                try {
                    $nodeLockManager->unlock($unlockNode, $this->getUser()->getId());
                } catch (\Exception $e) {
                    // unlock failed
                }
            }
        }

        if (!$this->isGranted('ROLE_SUPER_ADMIN') &&
            !$this->isGranted(array('permission' => 'EDIT', 'language' => $language), $node)
        ) {
            $doLock = false;
        }

        $lock = $nodeLockManager->findLock($node);

        if ($doLock && !$diff && !$lock) {
            $lock = $nodeLockManager->lock($node, $this->getUser()->getId());
        }

        $lockInfo = null;

        if ($lock && !$diff) {
            $lockUser = $userManager->find($lock->getUserId());

            $lockInfo = array(
                'status'   => 'locked',
                'nodeId'   => $lock->getNodeId(),
                'username' => $lockUser->getDisplayName(),
                'lockedAt' => $lock->getLockedAt()->format('Y-m-d H:i:s'),
                'age'      => time() - $lock->getLockedAt()->format('U'),
                'type'     => $lock->getType(),
            );

            if ($lock->getUserId() === $this->getUser()->getId()) {
                $lockInfo['status'] = 'edit';
            } elseif ($lock->getType() == NodeLock::TYPE_PERMANENTLY) {
                $lockInfo['status'] = 'locked_permanently';
            }
        } elseif ($diff) {
            // Workaround for loading diffs without locking and view-mask
            // TODO: introduce new diff lock mode

            $lockInfo = array(
                'status'   => 'edit',
                'id'       => '',
                'username' => '',
                'time'     => '',
                'age'      => 0,
                'type'     => NodeLock::TYPE_TEMPORARY,
            );
        }

        $elementtypeSerializer = new ElementtypeArraySerializer();
        $elementSerializer = new ElementVersionArraySerializer();

        $serializedStructure = $elementtypeSerializer->serialize($elementtypeStructure);
        $serializedValues = $elementSerializer->serialize($elementVersion, $language);

        $data = array(
            'success' => true,

            'nodeId'         => $node->getId(),
            'language'       => $language,
            'version'        => $node->getContentVersion(),
            'createdAt'      => $elementVersion->getCreatedAt()->format('Y-m-d H:i:s'),
            'createdBy'      => $elementVersion->getCreateUserId(),
            'masterLanguage' => $element->getMasterLanguage(),
            'isMaster'       => $language == $element->getMasterLanguage() ? true : false,

            'comment'             => $elementVersion->getComment(),
            'defaultTab'          => $elementtype->getDefaultTab(),
            'defaultContentTab'   => $elementtype->getDefaultContentTab(),
            'valueStructure'      => $serializedValues,
            'structure'           => $serializedStructure,

            'diff'                => $this->createDiff($node, $version, $compareVersion),
            'pager'               => $this->createPaging($node),
            'urls'                => $this->createUrls($node),
            'permissions'         => $this->createPermissions($node),
            'instances'           => $this->createInstances($node),
            'configuration'       => $this->createConfiguration($node),
            'allowedChildren'     => $this->createAllowedChildren($node),
            'versions'            => $this->createHistory($node),
            'lockInfo'            => $lockInfo,
        );

        $data = (object) $data;
        $event = new LoadDataEvent($node, $language, $data);
        $this->get('event_dispatcher')->dispatch(ElementEvents::LOAD_DATA, $event);
        $data = (array) $data;

        return new JsonResponse($data);
    }

    /**
     * Save element data
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="elements_data_save")
     */
    public function saveAction(Request $request)
    {
        $language = $request->get('language');

        $iconResolver = $this->get('phlexible_tree.icon_resolver');
        $dataSaver = $this->get('phlexible_element.request.data_saver');

        list($elementVersion, $node, $teaser, $publishSlaves) = $dataSaver->save($request, $this->getUser());

        $icon = $iconResolver->resolveNode($node);

        $msg = "Element {$elementVersion->getElement()->getEid()} master language {$elementVersion->getElement()->getMasterLanguage()} saved as new version {$elementVersion->getVersion()}";

        $data = array(
            'title'         => $elementVersion->getBackendTitle($language),
            'icon'          => $icon,
            'navigation'    => $teaser ? '' : $node->getInNavigation(),
            'restricted'    => $teaser ? '' : $node->getAttribute('needAuthentication'),
            'publish_other' => $publishSlaves,
            'publish'       => $request->get('publish'),
        );

        return new ResultResponse(true, $msg, $data);

        $tid = $request->get('tid');
        $eid = $request->get('eid');
        $data = $request->get('data');
        $oldVersion = $request->get('version');
        $comment = $request->get('comment');
        $isPublish = $request->get('publish');
        $notifications = $request->get('notifications');
        $values = $request->request->all();

        if ($data) {
            $data = json_decode($data, true);
        }

        $dispatcher = $this->get('event_dispatcher');
        $treeManager = $this->get('phlexible_tree.node_manager');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $elementHistoryManager = $this->get('phlexible_element.element_history_manager');

        $tree = $treeManager->getByNodeId($tid);
        $node = $tree->get($tid);
        $element = $elementService->findElement($eid);
        $elementtype = $elementService->findElementtype($element);
        $oldElementVersion = $elementService->findLatestElementVersion($element);
        $elementtypeVersion = $elementService->findElementtypeVersion($oldElementVersion);
        $oldLatestVersion = $oldElementVersion->getVersion();
        $isMaster = $element->getMasterLanguage() == $language;

        if ($teaser) {
            $elementHistoryManager->insert(
                ElementHistoryManagerInterface::ACTION_CREATE_ELEMENT_VERSION,
                $element->getEid(),
                $elementVersion->getCreateUserId(),
                null,
                $teaser->getId(),
                $elementVersion->getVersion(),
                $language,
                $comment
            );
        } else {
            $elementHistoryManager->insert(
                ElementHistoryManagerInterface::ACTION_CREATE_ELEMENT_VERSION,
                $element->getEid(),
                $elementVersion->getCreateUserId(),
                $node->getId(),
                null,
                $elementVersion->getVersion(),
                $language,
                $comment
            );
        }

        // save element structure
        if ($isMaster) {
            //$elementData->saveData($elementVersion, $values, $oldLatestVersion);
        } else {
            //$elementData->saveData($elementVersion, $values, $oldLatestVersion, $element->getMasterLanguage());
        }

        // update sort
        // TODO: repair

        if (0 && !$teaser) {
            $elementVersion->getBackendTitle($language);

            $select = $db
                ->select()
                ->distinct()
                ->from($db->prefix . 'element_tree', 'parent_id')
                ->where('eid = ?', $eid);

            $updateTids = $db->fetchCol($select);

            $parentNode = $node->getParentNode();
            if ($parentNode && $parentNode->getSortMode() != Tree::SORT_MODE_FREE) {
                foreach ($updateTids as $updateTid) {
                    if (!$updateTid) {
                        continue;
                    }

                    $sorter = $this->get('elementsTreeSorter');
                    $sorter->sortNode($parentNode);
                }
            }
        }

        $msg = 'Element "' . $eid . '" master language "' . $language . '" saved as new version ' . $newVersion;

        $publishOther = array();
        if ($isPublish) {
            $msg .= ' and published.';

            if (!$teaser) {
                $node = $treeManager->getNodeByNodeId($tid);
                $tree = $node->getTree();

                // notification data
                $notificationManager = $this->get('elementsNotifications');
                $checkNotify = $notificationManager->getNotificationByTid($tid, $language);

                // check if there is a notification already
                if (count($checkNotify) && $notifications) {
                    $notificationId = $checkNotify[0]['id'];
                    $notificationManager->update($notificationId, $language);
                } else {
                    if ($notifications) {
                        $notificationManager->save($tid, $language);
                    }
                }

                // publish node
                $tree->publishNode(
                    $node,
                    $language,
                    $newVersion,
                    false,
                    $comment
                );

                if (count($publishSlaveLanguages)) {
                    foreach ($publishSlaveLanguages as $slaveLanguage) {
                        // publish slave node
                        $tree->publishNode(
                            $node,
                            $slaveLanguage,
                            $newVersion,
                            false,
                            $comment
                        );

                        // workaround to fix missing catch results for non master language elements
                        Makeweb_Elements_Element_History::insert(
                            Makeweb_Elements_Element_History::ACTION_SAVE,
                            $eid,
                            $newVersion,
                            $slaveLanguage
                        );
                    }
                }
            } else {
                $tree = $node->getTree();

                $eid = $teasersManager->publish(
                    $teaserId,
                    $newVersion,
                    $language,
                    $comment,
                    $tid
                );

                if (count($publishSlaveLanguages)) {
                    foreach ($publishSlaveLanguages as $slaveLanguage) {
                        // publish slave node
                        $teasersManager->publish(
                            $teaserId,
                            $newVersion,
                            $slaveLanguage,
                            $comment,
                            $tid
                        );
                    }
                }
            }
        } else {
            $msg .= '.';
        }

        // remove locks

        $lockManager = $this->get('phlexible_element.element_lock_manager');
        $lockManager->unlock($element, $language);

        // queue update job
        // TODO: repair

        $queueService = $this->get('phlexible_queue.job_manager');

        /*
        $updateUsageJob = new Makeweb_Elements_Job_UpdateUsage();
        $updateUsageJob->setEid($eid);
        $queueService->addUniqueJob($updateUsageJob);

        $updateCatchHelperJob = new Makeweb_Teasers_Job_UpdateCatchHelper();
        $updateUsageJob->setEid($eid);
        $queueManager->addJob($updateCatchHelperJob);
        */

        // update file usage

        /*
        $fileUsage = new Makeweb_Elements_Element_FileUsage(MWF_Registry::getContainer()->dbPool);
        $fileUsage->update($eid);
        */

        $data = array();

        $status = '';
        if ($stateManager->isPublished($node, $language)) {
            $status = $stateManager->isAsync($node, $language) ? 'async' : 'online';
        }

        $data = array(
            'title'         => $elementVersion->getBackendTitle($language),
            'status'        => $status,
            'navigation'    => $teaserId ? '' : $node->getInNavigation($newVersion),
            'restricted'    => $teaserId ? '' : $node->getAttribute('restrictire'),
            'publish_other' => $publishSlaves,
        );

        return new ResultResponse(true, $msg, $data);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/urls", name="elements_data_urls")
     */
    public function urlsAction(Request $request)
    {
        $nodeId = $request->get('tid');
        $language = $request->get('language');

        $treeManager = $this->get('phlexible_tree.tree_manager');

        $treeContext = new WorkingTreeContext($language);
        $tree = $treeManager->getByNodeId($treeContext, $nodeId);
        $node = $tree->get($nodeId);

        $urls = array(
            'preview' => '',
            'online'  => '',
        );

        if ($node) {
            $urls['preview'] = $this->generateUrl('cms_preview', array('nodeId' => $nodeId, '_locale' => $language));

            try {
                $urls['online'] = $this->generateUrl($node);
            } catch (\Exception $e) {

            }
        }

        return new ResultResponse(true, '', $urls);
    }
}
