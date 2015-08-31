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
use Phlexible\Bundle\SiterootBundle\Form\Type\SiteType;
use Phlexible\Component\Site\Domain\Site;
use Phlexible\Component\Site\Domain\SiteCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Site controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_SITES')")
 * @Rest\NamePrefix("phlexible_api_site_")
 */
class SitesController extends FOSRestController
{
    /**
     * Get sites
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of Site",
     *   section="site",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getSitesAction()
    {
        $siteManager = $this->get('phlexible_siteroot.siteroot_manager');

        $sites = $siteManager->findAll();

        return new SiteCollection(
            $sites,
            count($sites)
        );
    }

    /**
     * Get sites
     *
     * @param string $siteId
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a Site",
     *   section="site",
     *   output="Phlexible\Component\Site\Domain\Site",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when site was not found"
     *   }
     * )
     */
    public function getSiteAction($siteId)
    {
        $siteManager = $this->get('phlexible_siteroot.siteroot_manager');
        $site = $siteManager->find($siteId);

        if (!$site instanceof Site) {
            throw new NotFoundHttpException('Site not found');
        }

        return $site;
    }

    /**
     * Create site
     *
     * @param Request $request
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Create a Site",
     *   section="site",
     *   input="Phlexible\Bundle\SiterootBundle\Form\Type\SiteType",
     *   statusCodes={
     *     201="Returned when site was created",
     *     204="Returned when site was updated",
     *     404="Returned when site was not found"
     *   }
     * )
     */
    public function postSitesAction(Request $request)
    {
        return $this->processForm($request, new Site());
    }

    /**
     * Update site
     *
     * @param Request $request
     * @param string  $siteId
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Update a Site",
     *   section="site",
     *   input="Phlexible\Bundle\SiterootBundle\Form\Type\SiteType",
     *   statusCodes={
     *     201="Returned when site was created",
     *     204="Returned when site was updated",
     *     404="Returned when site was not found"
     *   }
     * )
     */
    public function putSiteAction(Request $request, $siteId)
    {
        $siteManager = $this->get('phlexible_siteroot.siteroot_manager');
        $site = $siteManager->find($siteId);

        if (!$site instanceof Site) {
            throw new NotFoundHttpException('Site not found');
        }

        return $this->processForm($request, $site);
    }

    /**
     * @param Request $request
     * @param Site    $site
     *
     * @return Rest\View|Response
     */
    private function processForm(Request $request, Site $site)
    {
        $statusCode = !$site->getId() ? 201 : 204;

        $form = $this->createForm(new SiteType(), $site);
        $form->submit($request);

        if ($form->isValid()) {
            $siteManager = $this->get('phlexible_siteroot.siteroot_manager');
            $siteManager->updateSite($site);

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set(
                    'Location',
                    $this->generateUrl(
                        'phlexible_api_site_get_site',
                        array('siteId' => $site->getId()),
                        true
                    )
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }

    /**
     * Delete site
     *
     * @param string $siteId
     *
     * @return Response
     *
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Delete a Site",
     *   section="site",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the site is not found"
     *   }
     * )
     */
    public function deleteSiteAction($siteId)
    {
        $siteManager = $this->get('phlexible_siteroot.siteroot_manager');
        $site = $siteManager->find($siteId);

        if (!$site instanceof Site) {
            throw new NotFoundHttpException('Site not found');
        }

        $siteManager->deleteSite($site);
    }
}
