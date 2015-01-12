<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DashboardBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\DashboardBundle\Infobar\AbstractInfobar;
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
     * @ApiDoc(
     *   description="Returns a list of user portlets"
     * )
     */
    public function portletsAction()
    {
        $securityContext = $this->get('security.context');

        $portlets = $this->get('phlexible_dashboard.portlets');
        $infobars = $this->get('phlexible_dashboard.infobars');

        $data = array(
            'headerBar' => array(),
            'footerBar' => array(),
            'portlets'  => array(),
        );

        foreach ($infobars->all() as $infobar) {
            switch ($infobar->getRegion()) {
                case AbstractInfobar::REGION_HEADER:
                    $data['headerBar'][] = $infobar->toArray();
                    break;

                case AbstractInfobar::REGION_FOOTER:
                    $data['footerBar'][] = $infobar->toArray();
                    break;
            }

        }

        foreach ($portlets->all() as $portlet) {
            #if ($portlet->hasResource() && !$acl->isAllowed($currentUser, $portlet->getResource())) {
            #    continue;
            #}

            $data['portlets'][] = $portlet->toArray();
        }

        return new JsonResponse($data);
    }

    /**
     * Save portlets
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="dashboard_portlets_save")
     * @Method("POST")
     * @ApiDoc(
     *   description="Save user portlets",
     *   parameters={
     *     {"name"="portlets", "dataType"="array", "required"=true, "description"="Portlet data"}
     *   }
     * )
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
