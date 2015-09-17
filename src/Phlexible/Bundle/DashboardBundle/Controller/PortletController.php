<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\DashboardBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Portlet controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/dashboard/portlets")
 */
class PortletController extends Controller
{
    /**
     * Return portlets.
     *
     * @return JsonResponse
     * @Route("", name="dashboard_portlets")
     * @Method("GET")
     */
    public function portletsAction()
    {
        $authorizationChecker = $this->get('security.authorization_checker');

        $infobars = array();
        $portlets = array();

        foreach ($this->get('phlexible_dashboard.infobars')->all() as $infobar) {
            $infobars[] = $infobar->toArray();
        }

        foreach ($this->get('phlexible_dashboard.portlets')->all() as $portlet) {
            if (!$portlet->hasRole() || $authorizationChecker->isGranted($portlet->getRole())) {
                $portlets[] = $portlet->toArray();
            }
        }

        return new JsonResponse(array('infobars' => $infobars, 'portlets' => $portlets));
    }

    /**
     * Return portlets.
     *
     * @return JsonResponse
     * @Route("", name="dashboard_portlets_data")
     * @Method("GET")
     */
    public function dataAction()
    {
        $authorizationChecker = $this->get('security.authorization_checker');

        $infobars = array();
        $portlets = array();

        foreach ($this->get('phlexible_dashboard.infobars')->all() as $infobar) {
            $infobars[$infobar->getId()] = $infobar->getData();
        }

        foreach ($this->get('phlexible_dashboard.portlets')->all() as $portlet) {
            if (!$portlet->hasRole() || $authorizationChecker->isGranted($portlet->getRole())) {
                $portlets[$portlet->getId()] = $portlet->getData();
            }
        }

        return new JsonResponse(array('infobars' => $infobars, 'portlets' => $portlets));
    }
}
