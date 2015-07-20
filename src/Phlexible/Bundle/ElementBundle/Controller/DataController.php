<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\ElementStructure\Diff\Differ;
use Phlexible\Bundle\ElementBundle\ElementStructure\Serializer\ArraySerializer as ElementArraySerializer;
use Phlexible\Bundle\ElementBundle\Entity\ElementLock;
use Phlexible\Bundle\ElementBundle\Event\LoadDataEvent;
use Phlexible\Bundle\ElementBundle\Exception\InvalidArgumentException;
use Phlexible\Bundle\ElementBundle\Model\ElementHistoryManagerInterface;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\TreeBundle\Doctrine\TreeFilter;
use Phlexible\Component\Elementtype\ElementtypeStructure\Serializer\ArraySerializer as ElementtypeArraySerializer;
use Phlexible\Component\Elementtype\Model\Elementtype;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * Load element data
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/load", name="elements_data_load")
     */
    public function loadAction(Request $request)
    {
        $treeId = (int) $request->get('id');
        $teaserId = (int) $request->get('teaser_id');
        $language = $request->get('language');
        $version = $request->get('version');
        $unlockId = $request->get('unlock');
        $doLock = (bool) $request->get('lock', false);

        $diff = $request->get('diff');
        $diffVersionFrom = (int) $request->get('diff_version_from');
        $diffVersionTo = (int) $request->get('diff_version_to');
        $diffLanguage = $request->get('diff_language');

        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $treeManager = $this->get('phlexible_tree.tree_manager');
        $nodeManager = $this->get('phlexible_tree.node_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $iconResolver = $this->get('phlexible_element.icon_resolver');
        $elementHistoryManager = $this->get('phlexible_element.element_history_manager');
        $lockManager = $this->get('phlexible_element.element_lock_manager');
        $userManager = $this->get('phlexible_user.user_manager');

        $teaser = null;
        if ($teaserId) {
            $teaser = $teaserManager->find($teaserId);
            $eid = $teaser->getTypeId();
            $treeId = $teaser->getNodeId();
            $tree = $treeManager->getByNodeId($treeId);
            $tree->setDefaultLanguage($language);
            $node = $tree->get($treeId);
        } elseif ($treeId) {
            $tree = $treeManager->getByNodeId($treeId);
            $tree->setDefaultLanguage($language);
            $node = $tree->get($treeId);
            $eid = $node->getTypeId();
        } else {
            throw new InvalidArgumentException('Unknown data requested.');
        }

        $element = $elementService->findElement($eid);
        $elementMasterLanguage = $element->getMasterLanguage();

        if (!$language) {
            $language = $elementMasterLanguage;
        }

        if ($teaser) {
            $isPublished = $teaserManager->isPublished($teaser, $language);
            $onlineVersion = $teaserManager->getPublishedVersion($teaser, $language);
        } elseif ($treeId) {
            $isPublished = $tree->isPublished($node, $language);
            $onlineVersion = $tree->getPublishedVersion($node, $language);
        } else {
            throw new InvalidArgumentException('Unknown data requested.');
        }

        if (!$version) {
            $version = $element->getLatestVersion();
        }

        $elementVersion = $elementService->findElementVersion($element, $version);

        $elementtype = $elementService->findElementtype($element);
        $elementtypeStructure = $elementtype->getStructure();
        $type = $elementtype->getType();

        // versions

        if ($teaser) {
            $publishedVersions = $elementHistoryManager->findBy(
                array(
                    'teaserId' => $teaser->getId(),
                    'action'   => 'publishTeaser'
                )
            );
        } else {
            $publishedVersions = $elementHistoryManager->findBy(
                array(
                    'treeId' => $node->getId(),
                    'action' => 'publishNode'
                )
            );
        }

        $versions = array();
        foreach (array_reverse($elementService->getVersions($element)) as $version) {
            $versions[$version] = array(
                'version'      => $version,
                'format'       => 2,
                'createdAt'    => date('Y-m-d H:i:s'),
                'isPublished'  => false,
                'wasPublished' => false,
            );
        }

        foreach ($publishedVersions as $publishedVersion) {
            if (!$publishedVersion->getVersion()) {
                continue;
            }
            $versions[$publishedVersion->getVersion()]['isPublished'] = true;
            if ($publishedVersion->getVersion() === $onlineVersion) {
                $versions[$publishedVersion->getVersion()]['isPublished'] = true;
            } else {
                $versions[$publishedVersion->getVersion()]['wasPublished'] = true;
            }
        }

        $versions = array_values($versions);

        // instances

        $instances = array();
        if ($teaser) {
            foreach ($teaserManager->getInstances($teaser) as $instanceTeaser) {
                $instance = array(
                    'id'             => $instanceTeaser->getId(),
                    'instanceMaster' => false,
                    'modifiedAt'     => $instanceTeaser->getCreatedAt()->format('Y-m-d H:i:s'),
                    'icon'           => $iconResolver->resolveTeaser($instanceTeaser, $language),
                    'type'           => 'teaser',
                    'link'           => array(),
                );

                $instances[] = $instance;
            }
        } else {
            foreach ($nodeManager->getInstanceNodes($node->getNode()) as $instanceNode) {
                $instanceNodeContext = $treeManager->getByNodeId($instanceNode->getId())->get($instanceNode->getId());
                $instance = array(
                    'id'             => $instanceNode->getId(),
                    'instanceMaster' => false,
                    'modifiedAt'     => $instanceNode->getCreatedAt()->format('Y-m-d H:i:s'),
                    'icon'           => $iconResolver->resolveNode($instanceNodeContext, $language),
                    'type'           => 'treenode',
                    'link'           => array(),
                );

                if ($instanceNode->getSiterootId() !== $tree->getSiterootId()) {
                    $instance['link'] = array(
                        'start_tid_path' => '/' . implode('/', $treeManager->getByNodeId($instanceNode->getId())->getIdPath($instanceNode)),
                    );
                }

                $instances[] = $instance;
            }
        }

        // allowed child elements

        $allowedChildren = array();
        if (!$teaser) {
            foreach ($elementService->findAllowedChildren($elementtype) as $childElementtype) {
                if ($childElementtype->getType() !== 'full') {
                    continue;
                }

                $allowedChildren[] = array(
                    $childElementtype->getId(),
                    $childElementtype->getTitle(),
                    $iconResolver->resolveElementtype($childElementtype),
                );
            }
        }

        // diff

        if ($diff && $diffVersionFrom) {
            $elementStructure = $elementService->findElementStructure($elementVersion, $language);

            $fromElementVersion = $elementService->findElementVersion($element, $diffVersionFrom);
            $fromElementStructure = $elementService->findElementStructure($fromElementVersion);

            if ($diffVersionTo) {
                $toElementVersion = $elementService->findElementVersion($element, $diffVersionTo);
                $toElementStructure = $elementService->findElementStructure($toElementVersion);
            } else {
                $toElementStructure = $elementStructure;
            }

            if ($fromElementStructure !== $toElementStructure) {
                $differ = new Differ();
                $differ->diff($fromElementStructure, $toElementStructure);
            }

            $elementStructure = $fromElementStructure;
        }

        $diffInfo = null;
        if ($diff) {
            $diffInfo = array(
                'enabled'     => $diff,
                'versionFrom' => $diffVersionFrom,
                'versionTo'   => $diffVersionTo,
                'language'    => $diffLanguage,
            );
        }

        // lock

        if ($unlockId !== null) {
            $unlockElement = $elementService->findElement($unlockId);
            if ($unlockElement && $lockManager->isLockedByUser($unlockElement, $language, $this->getUser()->getId())) {
                try {
                    $lockManager->unlock($unlockElement, $this->getUser()->getId());
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

        $lock = null;
        if ($doLock && !$diff) {
            if (!$lockManager->isLockedByOtherUser($element, $language, $this->getUser()->getId())) {
                $lock = $lockManager->lock(
                    $element,
                    $this->getUser()->getId(),
                    $language
                );
            }
        }

        if (!$lock) {
            $lock = $lockManager->findMasterLock($element);
            if (!$lock) {
                $lock = $lockManager->findSlaveLock($element, $language);
            }
        }

        $lockInfo = null;

        if ($lock && !$diff) {
            $lockUser = $userManager->find($lock->getUserId());

            $lockInfo = array(
                'status'   => 'locked',
                'id'       => $lock->getElement()->getEid(),
                'username' => $lockUser->getDisplayName(),
                'time'     => $lock->getLockedAt()->format('Y-m-d H:i:s'),
                'age'      => time() - $lock->getLockedAt()->format('U'),
                'type'     => $lock->getType(),
            );

            if ($lock->getUserId() === $this->getUser()->getId()) {
                $lockInfo['status'] = 'edit';
            } elseif ($lock->getType() == ElementLock::TYPE_PERMANENTLY) {
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
                'type'     => ElementLock::TYPE_TEMPORARY,
            );
        }

        // meta

        $meta = array();
        $elementMetaSetResolver = $this->get('phlexible_element.element_meta_set_resolver');
        $elementMetaDataManager = $this->get('phlexible_element.element_meta_data_manager');
        $optionResolver = $this->get('phlexible_meta_set.option_resolver');
        $metaSetId = $elementtype->getMetaSetId();

        if ($metaSetId) {
            $metaSet = $elementMetaSetResolver->resolve($elementVersion);
            $metaData = $elementMetaDataManager->findByMetaSetAndElementVersion($metaSet, $elementVersion);

            $fieldDatas = array();

            foreach ($metaSet->getFields() as $field) {
                $options = $optionResolver->resolve($field);

                $fieldData = array(
                    'key'          => $field->getName(),
                    'type'         => $field->getType(),
                    'options'      => $options,
                    'readonly'     => $field->isReadonly(),
                    'required'     => $field->isRequired(),
                    'synchronized' => $field->isSynchronized(),
                );

                if ($metaData) {
                    foreach ($metaData->getLanguages() as $metaLanguage) {
                        if ($language === $metaLanguage) {
                            $fieldData['value'] = $metaData->get($field->getName(), $language);
                            break;
                        }
                    }
                }

                $fieldDatas[] = $fieldData;
            }

            $meta = array(
                'set_id' => $metaSetId,
                'title'  => $metaSet->getName(),
                'fields' => $fieldDatas
            );
        }

        // preview / online url

        $urls = array(
            'preview' => '',
            'online'  => '',
        );

        $publishDate = null;
        $publishUser = null;
        $onlineVersion = null;
        $latestVersion = null;

        if (in_array($elementtype->getType(), array(Elementtype::TYPE_FULL, Elementtype::TYPE_STRUCTURE, Elementtype::TYPE_PART))) {
            if ($type == Elementtype::TYPE_FULL) {
                $urls['preview'] = $this->generateUrl('cms_preview', array('treeId' => $node->getId(), '_locale' => $language));

                if ($isPublished) {
                    //$contentNode = $this->get('phlexible_tree.node_manager')->getByTreeId($node->getId())->get($node->getId());
                    $urls['online'] = $this->generateUrl($node->getId());
                }
            }

            if ($isPublished) {
                if ($teaser) {
                    $teaserOnline = $teaserManager->findOneOnlineByTeaserAndLanguage($teaser, $language);
                    $publishDate = $teaserOnline->getPublishedAt()->format('Y-m-d H:i:s');
                    $publishUser = $userManager->find($teaserOnline->getPublishUserId());
                    $onlineVersion = $teaserOnline->getVersion();
                } else {
                    $publishDate = $tree->getPublishedAt($node, $language)->format('Y-m-d H:i:s');
                    $publishUser = $userManager->find($tree->getPublishUserId($node, $language));
                    $onlineVersion = $tree->getPublishedVersion($node, $language);
                }
            }

            $latestVersion = $element->getLatestVersion();
        }

        // configuration

        if ($teaser) {
            $configuration = $teaser->getAttributes();
        } else {
            $configuration = $node->getAttributes();
            $configuration['navigation'] = $node->getInNavigation() ? true : false;
        }

        // context
        // TODO: repair element context

        $context = array();
        if (0) {
            $contextManager = $this->get('phlexible_element.context.manager');

            if ($contextManager->useContext()) {
                $contextCountries = $contextManager->getAllCountries();

                $activeContextCountries = $teaserId
                    ? $contextManager->getActiveCountriesByTeaserId($teaserId)
                    : $contextManager->getActiveCountriesByTid($node->getId());

                foreach ($contextCountries as $contextKey => $contextValue) {
                    $context[] = array(
                        'id'      => $contextKey,
                        'country' => $contextValue,
                        'active'  => in_array($contextKey, $activeContextCountries) ? 1 : 0
                    );
                }
            }
        }

        // pager

        $pager = array();
        if (!$teaser) {
            $parentNode = $tree->getParent($node);
            if ($parentNode) {
                $parentElement = $elementService->findElement($parentNode->getTypeId());
                $parentElementtype = $elementService->findElementtype($parentElement);
                if ($parentElementtype->getHideChildren()) {
                    $filter = new TreeFilter(
                        $this->get('doctrine.dbal.default_connection'),
                        $request->getSession(),
                        $this->get('event_dispatcher'),
                        $parentNode->getId(),
                        $language
                    );
                    $pager = $filter->getPager($node->getId());
                }
            }
        }

        // rights

        $permissions = array();
        $permissionRegistry = $this->get('phlexible_access_control.permission_registry');
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            if ($this->isGranted(array('permission' => 'VIEW', 'language' => $language), $node)) {
                return null;
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

        $elementtypeSerializer = new ElementtypeArraySerializer();
        $serializedStructure = $elementtypeSerializer->serialize($elementtypeStructure);

        $elementSerializer = new ElementArraySerializer();
        $serializedValues = $elementSerializer->serialize($elementVersion, $language);

        $data = array(
            'success' => true,

            'nodeId'         => $node->getId(),
            'type'           => $node->getType(),
            'eid'            => $eid,
            'language'       => $language,
            'version'        => $elementVersion->getVersion(),
            'createdAt'      => $elementVersion->getCreatedAt()->format('Y-m-d H:i:s'),
            'createdBy'      => $elementVersion->getCreateUserId(),
            'latestVersion'  => $element->getLatestVersion(),
            'masterLanguage' => $element->getMasterLanguage(),
            'isMaster'       => $language == $element->getMasterLanguage() ? true : false,

            'comment'             => $elementVersion->getComment(),
            'defaultTab'          => $elementtype->getDefaultTab(),
            'defaultContentTab'   => $elementtype->getDefaultContentTab(),
            'valueStructure'      => $serializedValues,
            'structure'           => $serializedStructure,
            'elementtypeId'       => $elementtype->getId(),
            'elementtypeRevision' => $elementVersion->getElementTypeVersion() . ' [' . $elementtype->getRevision() . ']',
            'elementtypeName'     => $elementtype->getUniqueId(),
            'elementtypeType'     => $elementtype->getType(),
            'meta'                => $meta,
            'diff'                => $diffInfo,

            'pager'               => $pager,
            'urls'                => $urls,
            'permissions'         => $permissions,
            'instances'           => $instances,
            'configuration'       => $configuration,
            'allowedChildren'     => $allowedChildren,
            'versions'            => $versions,
            'lockInfo'            => $lockInfo,
        );

        $data = (object) $data;
        $event = new LoadDataEvent($node, $teaser, $language, $data);
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
        $teaserId = $request->get('teaser_id');
        $language = $request->get('language');

        $iconResolver = $this->get('phlexible_element.icon_resolver');
        $dataSaver = $this->get('phlexible_element.request.data_saver');

        list($elementVersion, $node, $teaser, $publishSlaves) = $dataSaver->save($request, $this->getUser());

        if ($teaser) {
            $icon = $iconResolver->resolveTeaser($teaser, $language);
        } else {
            $icon = $iconResolver->resolveNode($node, $language);
        }

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

        // Copy meta values from old version to new version
        // TODO: repair

        $setId = $elementtypeVersion->getMetaSetId();
        if (0 && $setId) {
            $select = $db
                ->select()
                ->from($db->prefix . 'element_version_metaset_items')
                ->where('set_id = ?', $setId)
                ->where('eid = ?', $eid)
                ->where('version = ?', $oldLatestVersion);

            foreach ($db->fetchAll($select) as $insertData) {
                unset($insertData['id']);
                $insertData['version'] = $newVersion;

                $db->insert($db->prefix . 'element_version_metaset_items', $insertData);
            }
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

        $node = $treeManager->getByNodeId($nodeId)->get($nodeId);

        $urls = array(
            'preview' => '',
            'online'  => '',
        );

        if ($node) {
            $urls['preview'] = $this->generateUrl('cms_preview', array('treeId' => $nodeId, '_locale' => $language));

            if ($node->getTree()->isPublished($node, $language)) {
                try {
                    $urls['online'] = $this->generateUrl($node);
                } catch (\Exception $e) {

                }
            }
        }

        return new ResultResponse(true, '', $urls);
    }
}
