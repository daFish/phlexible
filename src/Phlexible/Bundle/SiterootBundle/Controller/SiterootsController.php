<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Controller;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Siteroot controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_SITEROOTS')")
 * @Prefix("/siteroot")
 * @NamePrefix("phlexible_siteroot_")
 */
class SiterootsController extends FOSRestController
{
    /**
     * Get siteroots
     *
     * @return Response
     *
     * @ApiDoc
     */
    public function getSiterootsAction()
    {
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroots = $siterootManager->findAll();

        return $this->handleView($this->view(
            array(
                'siteroots' => $siteroots,
                'count' => count($siteroots)
            )
        ));
    }

    /**
     * Get siteroots
     *
     * @param string $siterootId
     *
     * @return Response
     *
     * @View(templateVar="siteroot")
     * @ApiDoc
     */
    public function getSiterootAction($siterootId)
    {
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroot = $siterootManager->find($siterootId);

        return $siteroot;
    }

    /**
     * Create siteroot
     *
     * @param Siteroot $siteroot
     *
     * @return Response
     *
     * @ParamConverter("siteroot", converter="fos_rest.request_body")
     * @ApiDoc
     */
    public function postSiterootsAction(Siteroot $siteroot)
    {
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siterootManager->updateSiteroot($siteroot);

        return $this->handleView($this->view(
            array(
                'success' => true,
            )
        ));
    }

    /**
     * Update siteroot
     *
     * @param Siteroot $siteroot
     *
     * @return Response
     *
     * @ParamConverter("siteroot", converter="fos_rest.request_body")
     * @Put("/siteroots/{siterootId}")
     * @ApiDoc
     */
    public function putSiterootAction(Siteroot $siteroot, $siterootId)
    {
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siterootManager->updateSiteroot($siteroot);

        return $this->handleView($this->view(
            array(
                'success' => true,
            )
        ));
    }

    /**
     * Delete siteroot
     *
     * @param string $siterootId
     *
     * @return Response
     *
     * @ApiDoc
     */
    public function deleteSiterootAction($siterootId)
    {
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroot = $siterootManager->find($siterootId);
        $siterootManager->deleteSiteroot($siteroot);

        return $this->handleView($this->view(
            array(
                'success' => true,
            )
        ));
    }
}
