<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Event\TeaserEvent;
use Phlexible\Bundle\TeaserBundle\Exception\RuntimeException;
use Phlexible\Bundle\TeaserBundle\TeaserEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Layout controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/teasers/layout")
 * @Security("is_granted('ROLE_TEASERS')")
 */
class LayoutController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/tree", name="teasers_layout_tree")
     */
    public function treeAction(Request $request)
    {
        $language = $request->get('language');
        $nodeId = (int) $request->get('tid');

        if (!$nodeId || !$language) {
            return new Response('', 500);
        }

        $translator = $this->get('translator');
        $treeManager = $this->get('phlexible_tree.tree_manager');
        $areaManager = $this->get('phlexible_teaser.area_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $elementSourceManager = $this->get('phlexible_element.element_source_manager');
        $iconResolver = $this->get('phlexible_tree.icon_resolver');

        $this->get('phlexible_tree.mediator.element')->setVersionStrategy($this->get('phlexible_tree.mediator.preview_version_strategy'));

        $tree = $treeManager->getByNodeId($nodeId);
        $node = $tree->get($nodeId);
        $element = $elementService->findElement($node->getContentId());
        $elementMasterLanguage = $element->getMasterLanguage();
        $elementtype = $elementService->findElementtype($element);

        $layouts = array();
        $layoutareas = array();
        // TODO: repair
        foreach ($elementSourceManager->findElementtypesByType('layout') as $layoutarea) {
            if (in_array($elementtype, $elementService->findAllowedParents($layoutarea))) {
                $layoutareas[] = $layoutarea;
            }
        }

        foreach ($layoutareas as $layoutarea) {
            // TODO: switch to generic solution
            $availableLanguages = array(
                $language,
                'en',
                $elementMasterLanguage
            );

            $teasers = $areaManager->findCascadingByAreaAndNode($layoutarea, $node);
            // $language,
            // $availableLanguages
            // preview = true

            $areaRoot = array(
                'id'        => 'area_' . $layoutarea->getId(),
                'areaId'    => $layoutarea->getId(),
                'parentId'  => $nodeId,
                'text'      => $layoutarea->getTitle(),
                'icon'      => $iconResolver->resolveElementtype($layoutarea),
                'type'      => $layoutarea->getType(),
                'inherited' => null, //true,
                'cls'       => 'siteroot-node',
                'leaf'      => true,
                'expanded'  => true,
                'allowDrag' => true,
                'allowDrop' => false,
                'children'  => array(),
                'qtip'      => $translator->trans('elements.doubleclick_to_sort', array(), 'gui'),
            );

            foreach ($teasers as $teaser) {
                /* @var $teaser Teaser */

                $cls = '';
                if (!$teaser->isStopped($node)) {
                    $cls .= 'inherit ';
                }
                if ($teaser->isHidden($node)) {
                    $cls .= 'dont-show ';
                }
                if ($teaser->getParent()->getId() !== $nodeId) {
                    $cls .= 'inherited ';
                }

                $teaser->getField('backend', $language);

                $teaserData = array(
                    'id'        => $teaser->getId(),
                    'areaId'    => $layoutarea->getId(),
                    'parentId'  => $nodeId,
                    'type'      => $teaser->getContentType(),
                    'expanded'  => false,
                    'children'  => array(),
                    'allowDrag' => false,
                    'allowDrop' => false,
                    'text'      => $teaser->getField('backend', $language),
                    'icon'      => $iconResolver->resolveNode($teaser, $language),
                    'leaf'      => true,
                    'inherited' => $teaser->getParent()->getId() !== $nodeId,
                    'inherit'   => !$teaser->isStopped($node),
                    'cls'       => trim($cls),
                    'hide'      => $teaser->isHidden($node),
                );

                $areaRoot['children'][] = $teaserData;
            }

            if (count($areaRoot['children'])) {
                $areaRoot['leaf'] = false;
            }

            $layouts[] = $areaRoot;
        }

        return new JsonResponse($layouts);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/list", name="teasers_layout_list")
     */
    public function listAction(Request $request)
    {
        $nodeId = $request->get('tid');
        $layoutAreaId = $request->get('area_id');
        $language = $request->get('language');

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $iconResolver = $this->get('phlexible_tree.icon_resolver');

        $node = $treeManager->getByNodeId($nodeId)->get($nodeId);

        if (!$language) {
            $element = $elementService->findElement($node->getContentId());
            $language = $elementMasterLanguage = $element->getMasterLanguage();
        }

        $filter = $request->get('filter');
        if ($filter) {
            $filter = json_decode($filter, true);
        }

        $layoutarea = $elementtypeService->findElementtype($layoutAreaId);
        $teasers = $teaserManager->findCascadingForLayoutAreaAndNode($layoutarea, $node);

        $parent = array(
            'teaser_id'       => $nodeId, //(int) $teaserData->id,
            'tid'             => 0,
            'title'           => $node->getId(), //$teaserData->text,
            'element_type_id' => (int) $layoutarea->getId(),
            'element_type'    => $layoutarea->getName(),
            'icon'            => $iconResolver->resolveElementtype($layoutarea),
            'author'          => 'author',
            'version'         => $layoutarea->getRevision(),
            'create_time'     => $layoutarea->getCreatedAt(),
            //            'change_time'     => '2007-01-01 01:01:01',
            'publish_time'    => null,
            'language'        => $language,
            'sort'            => 0,
            'sort_mode'       => 'free',
            'sort_dir'        => 'asc',
            'version_latest'  => (int) $layoutarea->getRevision(),
            'version_online'  => (int) $layoutarea->getRevision(),
            'status'          => ' o_O ',
            'qtip'            =>
                $layoutarea->getName() . ', Version ' . $layoutarea->getRevision() . '<br>' .
                37 . ' Versions<br>'
        );

        $data = array();

        foreach ($teasers as $teaser) {
            /* @var $teaser Teaser */

            if ('element' == $teaser->getType()) {
                $teaserElement = $elementService->findElement($teaser->getTypeId());
                $teaserElementtype = $elementService->findElementtype($teaserElement);
                $teaserElementVersion = $elementService->findElementVersion($teaserElement, $teaserElement->getLatestVersion());

                if (!empty($filter['status'])) {
                    $status = explode(',', $filter['status']);

                    $show = false;
                    if (in_array('online', $status) && $teaserManager->isPublished($teaser, $language) && !$teaserManager->isAsync($teaser, $language)) {
                        $show = true;
                    } elseif (in_array('async', $status) && $teaserManager->isAsync($teaserElement, $language)) {
                        $show = true;
                    } elseif (in_array('offline', $status) && !$teaserManager->isPublished($teaserElement, $language)) {
                        $show = true;
                    }

                    if (!$show) {
                        continue;
                    }
                }

                $teaserOnline = $teaserManager->findOneStateByTeaserAndLanguage($teaser, $language);

                if (!empty($filter['date'])) {
                    $date = $filter['date'];
                    $dateFrom = !empty($filter['date_from']) ? strtotime($filter['date_from']) : '';
                    $dateTo = !empty($filter['date_to']) ? strtotime($filter['date_to']) : '';

                    $show = false;
                    if ($date === 'create') {
                        $createdAt = $teaserElementVersion->getCreatedAt();

                        if ((!$dateFrom || $createdAt > $dateFrom) && (!$dateTo || $createdAt < $dateTo)) {
                            $show = true;
                        }
                    } elseif ($date === 'publish') {
                        $publishedAt = null;
                        if ($teaserOnline) {
                            $publishedAt = $teaserOnline->getPublishedAt();
                        }

                        if ((!$dateFrom || $publishedAt > $dateFrom) && (!$dateTo || $publishedAt < $dateTo)) {
                            $show = true;
                        }
                    } elseif ($date === 'custom') {
                        $customDate = $teaserElementVersion->getCustomDate($language);

                        if ((!$dateFrom || $customDate > $dateFrom) && (!$dateTo || $customDate < $dateTo)) {
                            $show = true;
                        }
                    }

                    if (!$show) {
                        continue;
                    }
                }

                $data[] = array(
                    'teaser_id'       => $teaser->getId(),
                    '_type'           => $teaser->getType(),
                    'eid'             => $teaser->getTypeId(),
                    'title'           => $teaserElementVersion->getBackendTitle($language),
                    'element_type_id' => (int) $teaserElement->getElementtypeId(),
                    'element_type'    => $teaserElementtype->getName(),
                    'navigation'      => 0,
                    'restricted'      => 0,
                    'icon'            => $iconResolver->resolveTeaser($teaser, $language),
                    'author'          => 'author',
                    'version'         => $teaserElementVersion->getVersion(),
                    'create_time'     => $teaserElementVersion->getCreatedAt()->format('Y-m-d H:i:s'),
                    //                'change_time'     => $child['modify_time'],
                    'publish_time'    => $teaserOnline ? $teaserOnline->getPublishedAt() : '',
                    'custom_date'     => $teaserElementVersion->getCustomDate($language),
                    'language'        => $language,
                    'sort'            => (int) $teaser->getSort(),
                    'version_latest'  => (int) $teaserElement->getLatestVersion(),
                    'version_online'  => (int) $teaserManager->getPublishedVersion($teaser, $language),
                    'status'          => '>o>',
                    'qtip'            => $teaserElementVersion->getBackendTitle($language) . ', Version ' . $teaserElementVersion->getElementtypeVersion() . '<br>' .
                        'Version ' . $teaserElementVersion->getVersion() . '<br>',
                );
            } elseif ('inherited' == $teaser->getType()) {
                $data[] = array(
                    'teaser_id'       => (int) $teaser->getId(),
                    'eid'             => null,
                    '_type'           => $teaser->getType(),
                    'title'           => 'waaa', //$teaserItem->text,
                    'element_type_id' => 0,
                    'element_type'    => '',
                    'navigation'      => 0,
                    'restricted'      => 0,
                    'icon'            => $iconResolver->resolveTeaser($teaser, $language),
                    'author'          => 'author',
                    'version'         => 0,
                    'create_time'     => '',
                    //                'change_time'     => $child['modify_time'],
                    'publish_time'    => null,
                    'language'        => $language,
                    'sort'            => $teaser->getSort(),
                    'version_latest'  => 0,
                    'version_online'  => 0,
                    'status'          => '>o>',
                    'qtip'            => 'waaa', //$teaserItem->text,
                );
            }
        }

        //$data['totalChilds'] = $element->getChildCount();

        return new JsonResponse(
            array(
                'parent' => $parent,
                'list'   => $data
            )
        );
    }

    /**
     * List all element child elementtypes
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/childelementtypes", name="teasers_layout_childelementtypes")
     */
    public function childElementtypesAction(Request $request)
    {
        $defaultLanguage = $this->container->getParameter('phlexible_cms.languages.default');

        $id = $request->get('id');

        $elementSourceManager = $this->get('phlexible_element.element_source_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $iconResolver = $this->get('phlexible_tree.icon_resolver');

        $elementtype = $elementSourceManager->findElementtype($id);
        $childElementtypes = $elementService->findAllowedChildren($elementtype);

        $data = array();
        foreach ($childElementtypes as $childElementtype) {
            $data[$childElementtype->getName() . $childElementtype->getId()] = array(
                'id'   => $childElementtype->getId(),
                'name' => $childElementtype->getName(),
                'icon' => $iconResolver->resolveElementtype($childElementtype),
            );
        }
        ksort($data);
        $data = array_values($data);

        return new JsonResponse(array('elementtypes' => $data));
    }

    /**
     * List all child element types
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/childelements", name="teasers_layout_childelements")
     */
    public function childElementsAction(Request $request)
    {
        $tid = $request->get('tree_id');
        $layoutareaId = $request->get('layoutarea_id');
        $language = $request->get('language', 'de');

        $translator = $this->get('translator');
        $treeManager = $this->get('phlexible_tree.node_manager');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $iconResolver = $this->get('phlexible_tree.icon_resolver');

        $data = array();
        $data[] = array(
            'id'    => '0',
            'title' => $translator->trans('elements.first', array(), 'gui'),
            'icon'  => $iconResolver->resolveIcon('_top.gif'),
        );

        $tree = $treeManager->getByNodeId($tid);
        $node = $tree->get($tid);

        $layoutarea = $elementtypeService->findElementtype($layoutareaId);
        $teasers = $teaserManager->findCascadingForLayoutAreaAndNode($layoutarea, $node);

        foreach ($teasers as $teaser) {
            $teaserElement = $elementService->findElement($teaser->getTypeId());
            $teaserElementVersion = $elementService->findElementVersion($teaserElement, $teaserElement->getLatestVersion());
            $data[] = array(
                'id'    => $teaser->getId(),
                'title' => $teaserElementVersion->getBackendTitle($language),
                'icon'  => $iconResolver->resolveTeaser($teaser, $language),
            );
        }

        return new JsonResponse(array('elements' => $data));
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="teasers_layout_createteaser")
     */
    public function createAction(Request $request)
    {
        $siterootId = $request->get('siteroot_id');
        $nodeId = $request->get('tree_id');
        $eid = $request->get('eid');
        $layoutareaId = $request->get('layoutarea_id');
        $elementtypeId = $request->get('element_type_id');
        $prevId = $request->get('prev_id', 0);
        $inherit = $request->get('inherit') == 'on' ? true : false;
        $show = $request->get('shown_here') == 'on' ? true : false;
        $masterLanguage = $request->get('masterlanguage', null);

        if (!$masterLanguage) {
            $masterLanguage = $this->container->getParameter('phlexible_cms.languages.default');
        }

        $elementService = $this->get('phlexible_element.element_service');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $treeManager = $this->get('phlexible_tree.tree_manager');

        $node = $treeManager->getByNodeId($nodeId)->get($nodeId);
        $elementSource = $elementService->findElementSource($elementtypeId);
        $userId = $this->getUser()->getId();

        $elementVersion = $elementService->createElement($elementSource, $masterLanguage, $userId);
        $element = $elementVersion->getElement();

        $stopIds = array();
        if (!$inherit) {
            $stopIds[] = $nodeId;
        }

        $hideIds = array();
        if (!$show) {
            $hideIds[] = $nodeId;
        }

        $teaser = $teaserManager->createTeaser(
            $node,
            $eid,
            $layoutareaId,
            'element',
            $element->getEid(),
            $prevId,
            $stopIds,
            $hideIds,
            $masterLanguage,
            $userId
        );

        return new ResultResponse(true, "Teaser with ID {$teaser->getId()} created.", array('language' => $masterLanguage));
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/createinstance", name="teasers_layout_createinstance")
     */
    public function createInstanceAction(Request $request)
    {
        $nodeId = $request->get('tid');
        $layoutAreaId = $request->get('id');
        $teaserId = $request->get('for_teaser_id');

        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $treeManager = $this->get('phlexible_tree.node_manager');

        $teaser = $teaserManager->find($teaserId);
        $node = $treeManager->getByNodeId($nodeId)->get($nodeId);

        $teaserManager->createTeaserInstance($node, $teaser, $layoutAreaId, $this->getUser()->getId());

        return new ResultResponse(true, 'Instance created.');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/delete", name="teasers_layout_delete")
     */
    public function deleteAction(Request $request)
    {
        $teaserId = $request->get('teaser_id');
        $type = $request->get('type');

        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $elementService = $this->get('phlexible_element.element_service');

        $teaser = $teaserManager->find($teaserId);
        if ($teaser->getType() === 'element') {
            $element = $elementService->findElement($teaser->getTypeId());
            $elementService->deleteElement($element);
        }

        foreach ($teaserManager->findBy(array('type' => array('sort', 'stop', 'inherit'), 'typeId' => $teaser->getTypeId())) as $subTeaser) {
            $teaserManager->deleteTeaser($subTeaser, $this->getUser()->getId());
        }

        $teaserManager->deleteTeaser($teaser, $this->getUser()->getId());

        // TODO: fix
        /*
        $job = new Makeweb_Elements_Job_UpdateUsage();
        $job->setEid($eid);
        */

        return new ResultResponse(true, "Teaser {$teaser->getId()} deleted.");
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/inherit", name="teasers_layout_inherit")
     */
    public function inheritAction(Request $request)
    {
        $nodeId = (int) $request->get('tree_id');
        $teaserId = (int) $request->get('teaser_id');

        $dispatcher = $this->get('event_dispatcher');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $teaser = $teaserManager->find($teaserId);
        $teaser->removeStopId($nodeId);

        $event = new TeaserEvent($teaser, $nodeId);
        if ($dispatcher->dispatch(TeaserEvents::BEFORE_INHERIT_TEASER, $event)->isPropagationStopped()) {
            throw new RuntimeException('Inherit cancelled by event');
        }

        $teaserManager->updateTeaser($teaser);

        $event = new TeaserEvent($teaser, $nodeId);
        $dispatcher->dispatch(TeaserEvents::INHERIT_TEASER, $event);

        return new ResultResponse(true, 'Inheritance stop removed');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/stop", name="teasers_layout_stop")
     */
    public function stopAction(Request $request)
    {
        $nodeId = (int) $request->get('tree_id');
        $teaserId = (int) $request->get('teaser_id');

        $dispatcher = $this->get('event_dispatcher');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $teaser = $teaserManager->find($teaserId);
        $teaser->addStopId($nodeId);

        $event = new TeaserEvent($teaser, $nodeId);
        if ($dispatcher->dispatch(TeaserEvents::BEFORE_STOP_TEASER, $event)->isPropagationStopped()) {
            throw new RuntimeException('Stop inherit cancelled by event');
        }

        $teaserManager->updateTeaser($teaser);

        $event = new TeaserEvent($teaser, $nodeId);
        $dispatcher->dispatch(TeaserEvents::STOP_TEASER, $event);

        return new ResultResponse(true, 'Inheritance stopped');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/show", name="teasers_layout_show")
     */
    public function showAction(Request $request)
    {
        $nodeId = (int) $request->get('tree_id');
        $teaserId = (int) $request->get('teaser_id');

        $dispatcher = $this->get('event_dispatcher');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $teaser = $teaserManager->find($teaserId);
        $teaser->removeHideId($nodeId);

        $beforeEvent = new TeaserEvent($teaser, $nodeId);
        if ($dispatcher->dispatch(TeaserEvents::BEFORE_SHOW_TEASER, $beforeEvent)->isPropagationStopped()) {
            throw new RuntimeException('Show cancelled by event');
        }

        $teaserManager->updateTeaser($teaser);

        $event = new TeaserEvent($teaser, $nodeId);
        $dispatcher->dispatch(TeaserEvents::SHOW_TEASER, $event);

        return new ResultResponse(true, 'Teaser will be displayed.');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/hide", name="teasers_layout_hide")
     */
    public function hideAction(Request $request)
    {
        $nodeId = (int) $request->get('tree_id');
        $teaserId = (int) $request->get('teaser_id');

        $dispatcher = $this->get('event_dispatcher');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');

        $teaser = $teaserManager->find($teaserId);
        $teaser->addHideId($nodeId);

        $beforeEvent = new TeaserEvent($teaser, $nodeId);
        if ($dispatcher->dispatch(TeaserEvents::BEFORE_HIDE_TEASER, $beforeEvent)->isPropagationStopped()) {
            throw new RuntimeException('Show cancelled by event');
        }

        $teaserManager->updateTeaser($teaser);

        $event = new TeaserEvent($teaser, $nodeId);
        $dispatcher->dispatch(TeaserEvents::HIDE_TEASER, $event);

        return new ResultResponse(true, 'Teaser will not be displayed.');
    }

    /**
     * @Route("/sort", name="teasers_layout_sort")
     */
    public function sortAction()
    {
        // TODO use Brainbits_Filter_Input
        $nodeId = $this->_getParam('tid');
        $eid = $this->_getParam('eid');
        $layoutAreaId = $this->_getParam('area_id');
        $sortIds = $this->_getParam('sort_ids');
        $sortIds = json_decode($sortIds, true);

        $dispatcher = $this->getContainer()->get('event_dispatcher');

        try {
            $beforeEvent = new Makeweb_Teasers_Event_BeforeReorderTeasers($nodeId, $eid, $layoutAreaId, $sortIds);
            if (false === $dispatcher->dispatch($beforeEvent)) {
                throw new RuntimeException('Teaser sort cancelled by event');
            }

            $db = $this->getContainer()->dbPool->default;

            $db->beginTransaction();

            $select = $db->select()
                ->from($db->prefix . 'element_tree_teasers', 'layoutarea_id')
                ->where('id = :teaserId');

            foreach ($sortIds as $sort => $teaserId) {
                if (!$teaserId) {
                    continue;
                }

                if (-1 == $teaserId) {
                    $insertData = array(
                        'node_id'       => $nodeId,
                        'eid'           => $eid,
                        'layoutarea_id' => $layoutAreaId,
                        'teaser_eid'    => null,
                        'type'          => Makeweb_Teasers_Manager::TYPE_INHERITED,
                        'sort'          => $sort,
                        'modify_uid'    => MWF_Env::getUid(),
                        'modify_time'   => $db->fn->now(),
                    );

                    $db->insert($db->prefix . 'element_tree_teasers', $insertData);

                    $teaserId = $db->lastInsertId($db->prefix . 'element_tree_teasers');

                    continue;
                }

                $exists = $db->fetchOne($select, array('teaserId' => $teaserId)) ? true : false;

                if (!$exists) {
                    continue;
                }

                $db->update(
                    $db->prefix . 'element_tree_teasers',
                    array('sort' => $sort),
                    array('id = ?' => $teaserId)
                );
            }

            $db->commit();
            $event = new Makeweb_Teasers_Event_ReorderTeasers($nodeId, $eid, $layoutAreaId, $sortIds);
            $dispatcher->dispatch($event);

            $result = MWF_Ext_Result::encode(true, null, 'Teaser sort published.');
        } catch (Exception $e) {
            $db->rollback();

            $result = MWF_Ext_Result::encode(false, null, $e->getMessage());
        }

        $this->getResponse()->setAjaxPayload($result);
    }

    /**
     * Return the Element data tree
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/reference", name="teasers_layout_reference")
     */
    public function referenceAction(Request $request)
    {
        // TODO: switch to master language of element
        $defaultLanguage = $this->container->getParameter('phlexible_cms.languages.default');

        $siterootId = $request->get('siteroot_id');
        $tid = $request->get('node');
        $language = $request->get('language');

        $treeManager = $this->get('phlexible_tree.node_manager');
        $elementService = $this->get('phlexible_element.element_service');
        $translator = $this->get('translator');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $elementSourceManager = $this->get('phlexible_element.element_source_manager');
        $nodeSerializer = $this->get('phlexible_tree.node_serializer');
        $iconResolver = $this->get('phlexible_tree.icon_resolver');

        $tree = $treeManager->getBySiteRootId($siterootId);

        $node = $tree->get($tid);
        $rootNode = $tree->getRoot();

        $data = array();
        if ($rootNode !== null) {
            if ($tid === null || $tid < 1) {
                $data = array($nodeSerializer->serializeNode($rootNode, $language));

                return new JsonResponse($data);
            }

            // check if children of this node should be shown
            $element = $elementService->findElement($node->getTypeId());
            $elementtype = $elementService->findElementtype($element);

            $nodes = $tree->getChildren($node);
            if (!empty($nodes) && !$elementtype->getHideChildren()) {
                $data = $nodeSerializer->serializeNodes($nodes, $language);
            }

            foreach ($data as $key => $row) {
                if ($row['leaf']) {
                    unset($data[$key]);
                    continue;
                }
                $data[$key]['cls'] = (!empty($data[$key]['cls']) ? $data[$key]['cls'] . ' ' : '') . 'node-disabled';
            }
        }

        $currentTreeId = $tid;

        $element = $elementService->findElement($node->getTypeId());
        $elementMasterLanguage = $element->getMasterLanguage();
        $elementtype = $elementService->findElementtype($element);

        $layouts = array();
        $layoutareas = array();
        // TODO: repair
        foreach ($elementSourceManager->findElementtypesByType('layout') as $layoutarea) {
            if (in_array($elementtype, $elementService->findAllowedParents($layoutarea))) {
                $layoutareas[] = $layoutarea;
            }
        }

        foreach ($layoutareas as $layoutarea) {
            $areaRoot = array(
                'id'         => 'area_' . $layoutarea->getId(),
                'area_id'    => $layoutarea->getId(),
                'parent_tid' => $currentTreeId,
                'parent_eid' => $element->getEid(),
                'icon'       => $iconResolver->resolveElementtype($layoutarea),
                'text'       => $layoutarea->getTitle(),
                'type'       => 'area',
                'inherited'  => null, //true,
                'leaf'       => false,
                'expanded'   => true,
                'allowDrag'  => false,
                'allowDrop'  => false,
                'children'   => array(),
                'qtip'       => $translator->trans('elements.doubleclick_to_sort', array(), 'gui'),
            );

            $teasers = $teaserManager->findForLayoutAreaAndNodeContext($layoutarea, $node);

            foreach ($teasers as $teaser) {
                switch ($teaser->getType()) {
                    case 'element':
                        $teaserElement = $elementService->findElement($teaser->getTypeId());
                        $teaserElementVersion = $elementService->findElementVersion($teaserElement, $teaserElement->getLatestVersion());

                        $areaRoot['children'][] = array(
                            'id'            => $teaser->getId(),
                            'parent_tid'    => $currentTreeId,
                            'parent_eid'    => $element->getEid(),
                            'layoutarea_id' => $layoutarea->getId(),
                            'icon'          => $iconResolver->resolveTeaser($teaser, $language),
                            'text'          => $teaserElementVersion->getBackendTitle($language),
                            // . ' [' . $teaser->getEid() . ']',
                            'eid'           => $teaser->getTypeId(),
                            'type'          => 'teaser',
                            'expanded'      => false,
                            'leaf'          => true,
                            'allowDrag'     => false,
                            'allowDrop'     => false,
                            'children'      => array()
                        );

                        break;
                }
            }

            if (count($areaRoot['children'])) {
                $data[] = $areaRoot;
            }
        }

        $data = array_values($data);

        return new JsonResponse($data);
    }
}
