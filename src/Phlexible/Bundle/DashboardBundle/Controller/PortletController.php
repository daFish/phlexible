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

use Phlexible\Bundle\DashboardBundle\Infobar\Infobar;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Portlet controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/dashboard/portlets")
 */
class PortletController extends Controller
{
    /**
     * Return portlets
     *
     * @return JsonResponse
     * @Route("", name="dashboard_portlets")
     * @Method("GET")
     */
    public function portletsAction()
    {
        $authorizationChecker = $this->get('security.authorization_checker');

        $headers = array();
        $footers = array();
        $portlets = array();

        foreach ($this->get('phlexible_dashboard.infobars')->all() as $infobar) {
            if ($infobar->getRegion() === Infobar::REGION_HEADER) {
                $headers[] = $infobar->toArray();
            } else {
                $footers[] = $infobar->toArray();
            }
        }

        foreach ($this->get('phlexible_dashboard.portlets')->all() as $portlet) {
            if (!$portlet->hasRole() || $authorizationChecker->isGranted($portlet->getRole())) {
                $portlets[] = $portlet->toArray();
            }
        }

        return new JsonResponse(array('headerBar' => $headers, 'footerBar' => $footers, 'portlets' => $portlets));
    }

    /**
     * Save portlets
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="dashboard_portlets_save")
     * @Method("POST")
     */
    public function saveAction(Request $request)
    {
        $portlets = $request->request->get('portlets');
        $portlets = json_decode($portlets, true);

        if (!is_array($portlets)) {
            return new ResultResponse(false, 'Portlets data invalid.');
        }

        $user = $this->getUser();
        $user->setProperty('portlets', json_encode($portlets));

        $this->get('phlexible_user.user_manager')->updateUser($user);

        return new ResultResponse(true, 'Portlets saved.');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/columns", name="dashboard_portlets_columns")
     * @method("POST")
     */
    public function columnsAction(Request $request)
    {
        $count = $request->request->get('column');

        $user = $this->getUser();

        $user->setProperty('dashboard.columns', $count);

        $response = new ResultResponse();
        $response->setResult(true, 'Columns changed.');

        return $response;
    }
}
