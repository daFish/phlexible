<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Component\Site\Domain\Site;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Siteroot controller
 *
 * @author Phillip Look <plook@brainbits.net>
 * @Route("/siteroots/siteroot")
 * @Security("is_granted('ROLE_SITEROOTS')")
 */
class SiterootController extends Controller
{
    /**
     * List siteroots
     *
     * @return JsonResponse
     * @Route("/list", name="siteroots_siteroot_list")
     */
    public function listAction()
    {
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroots = array();
        foreach ($siterootManager->findAll() as $siteroot) {
            $siteroots[] = array(
                'id'    => $siteroot->getId(),
                'title' => $siteroot->getTitle(),
            );
        }

        return new JsonResponse(array(
            'siteroots' => $siteroots,
            'count'     => count($siteroots)
        ));
    }

    /**
     * Create siteroot
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="siteroots_siteroot_create")
     */
    public function createAction(Request $request)
    {
        $title = $request->get('title', null);

        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroot = new Site();
        foreach (explode(',', $this->container->getParameter('phlexible_gui.languages.available')) as $language) {
            $siteroot->setTitle($language, $title);
        }
        $siteroot
            ->setCreateUserId($this->getUser()->getId())
            ->setCreatedAt(new \DateTime())
            ->setModifyUserId($siteroot->getCreateUserId())
            ->setModifiedAt($siteroot->getCreatedAt());

        $siterootManager->updateSiteroot($siteroot);

        return new ResultResponse(true, 'New Siteroot created.');
    }

    /**
     * Delete siteroot
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/delete", name="siteroots_siteroot_delete")
     */
    public function deleteAction(Request $request)
    {
        $siterootId = $request->get('id');

        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroot = $siterootManager->find($siterootId);
        $siterootManager->deleteSiteroot($siteroot);

        return new ResultResponse(true, 'Siteroot deleted.');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="siteroots_siteroot_load")
     */
    public function loadAction(Request $request)
    {
        $siterootId = $request->get('id');

        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroot = $siterootManager->find($siterootId);

        $data = array(
            'titles'          => $siteroot->getTitles(),
            'hostname'        => $siteroot->getHostname(),
            'navigations'     => array(),
            'properties'      => array(),
            'specialtids'     => array(),
        );

        foreach ($siteroot->getNavigations() as $navigation) {
            $data['navigations'][] = array(
                'name'     => $navigation->getName(),
                'nodeId'   => $navigation->getNodeId(),
                'maxDepth' => $navigation->getMaxDepth(),
            );
        }

        foreach ($siteroot->getProperties() as $key => $value) {
            $data['properties'][$key] = strlen($value) ? $value : '';
        }

        foreach ($siteroot->getNodeAliases() as $nodeAlias) {
            $data['nodeAliases'][] = array(
                'key'      => $nodeAlias->getName(),
                'language' => $nodeAlias->getLanguage(),
                'nodeId'   => $nodeAlias->getNodeId(),
            );
        }

        foreach ($siteroot->getEntryPoints() as $entryPoint) {
            $data['entryPoints'][] = array(
                'name'     => $entryPoint->getName(),
                'hostname' => $entryPoint->getHostname(),
                'language' => $entryPoint->getLanguage(),
                'nodeId'   => $entryPoint->getNodeId(),
            );
        }

        foreach ($siteroot->getNodeConstraints() as $nodeConstraint) {
            $data['nodeConstraints'][$nodeConstraint->getName()] = array(
                'name'      => $nodeConstraint->getName(),
                'allowed'   => $nodeConstraint->isAllowed(),
                'nodeTypes' => $nodeConstraint->getNodeTypes(),
            );
        }

        return new JsonResponse($data);
    }

    /**
     * Save siteroot
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="siteroots_siteroot_save")
     */
    public function saveAction(Request $request)
    {
        $siterootSaver = $this->get('phlexible_siteroot.siteroot_saver');

        $siterootSaver->saveAction($request);

        return new ResultResponse(true, 'Siteroot data saved');
    }
}
