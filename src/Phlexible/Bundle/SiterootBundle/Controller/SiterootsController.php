<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Form\Type\SiterootType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Siteroot controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_SITEROOTS')")
 * @Rest\NamePrefix("phlexible_api_siteroot_")
 */
class SiterootsController extends FOSRestController
{
    /**
     * Get siteroots
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of Siteroot",
     *   section="siteroot",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getSiterootsAction()
    {
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroots = $siterootManager->findAll();

        return array(
            'siteroots' => $siteroots,
            'count'     => count($siteroots)
        );
    }

    /**
     * Get siteroots
     *
     * @param string $siterootId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a Siteroot",
     *   section="siteroot",
     *   output="Phlexible\Bundle\SiterootBundle\Entity\Siteroot",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when siteroot was not found"
     *   }
     * )
     */
    public function getSiterootAction($siterootId)
    {
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');
        $siteroot = $siterootManager->find($siterootId);

        if (!$siteroot instanceof Siteroot) {
            throw new NotFoundHttpException('Siteroot not found');
        }

        return array(
            'siteroot' => $siteroot
        );
    }

    /**
     * Create siteroot
     *
     * @param Request $request
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Create a Siteroot",
     *   section="siteroot",
     *   input="Phlexible\Bundle\SiterootBundle\Form\Type\SiterootType",
     *   statusCodes={
     *     201="Returned when siteroot was created",
     *     204="Returned when siteroot was updated",
     *     404="Returned when siteroot was not found"
     *   }
     * )
     */
    public function postSiterootsAction(Request $request)
    {
        return $this->processForm($request, new Siteroot());
    }

    /**
     * Update siteroot
     *
     * @param Request $request
     * @param string  $siterootId
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Update a Siteroot",
     *   section="siteroot",
     *   input="Phlexible\Bundle\SiterootBundle\Form\Type\SiterootType",
     *   statusCodes={
     *     201="Returned when siteroot was created",
     *     204="Returned when siteroot was updated",
     *     404="Returned when siteroot was not found"
     *   }
     * )
     */
    public function putSiterootAction(Request $request, $siterootId)
    {
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');
        $siteroot = $siterootManager->find($siterootId);

        if (!$siteroot instanceof Siteroot) {
            throw new NotFoundHttpException('Siteroot not found');
        }

        return $this->processForm($request, $siteroot);
    }

    /**
     * @param Request  $request
     * @param Siteroot $siteroot
     *
     * @return Rest\View|Response
     */
    private function processForm(Request $request, Siteroot $siteroot)
    {
        $statusCode = !$siteroot->getId() ? 201 : 204;

        $form = $this->createForm(new SiterootType(), $siteroot);
        $form->submit($request);

        if ($form->isValid()) {
            $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');
            $siterootManager->updateSiteroot($siteroot);

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'phlexible_api_siteroot_get_siteroot', array('siterootId' => $siteroot->getId()),
                        true // absolute
                    )
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }

    /**
     * Delete siteroot
     *
     * @param string $siterootId
     *
     * @return Response
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Delete a Siteroot",
     *   section="siteroot",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the siteroot is not found"
     *   }
     * )
     */
    public function deleteSiterootAction($siterootId)
    {
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');
        $siteroot = $siterootManager->find($siterootId);

        if (!$siteroot instanceof Siteroot) {
            throw new NotFoundHttpException('Siteroot not found');
        }

        $siterootManager->deleteSiteroot($siteroot);
    }
}
