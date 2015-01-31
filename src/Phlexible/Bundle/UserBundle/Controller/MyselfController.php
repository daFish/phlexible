<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Myself controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Prefix("/user")
 * @NamePrefix("phlexible_user_")
 */
class MyselfController extends Controller
{
    /**
     * Get information about current user
     *
     * @return Response
     *
     * @Get()
     * @View()
     * @ApiDoc()
     */
    public function getMyselfAction()
    {
        $user = $this->getUser();

        return $user;
    }

    /**
     * Update current user
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Patch()
     * @ApiDoc()
     */
    public function patchMyselfAction(Request $request)
    {
        $user = $this->getUser();

        if ($request->request->has('firstname')) {
            $user->setFirstname($request->request->get('firstname'));
        }

        if ($request->request->has('lastname')) {
            $user->setLastname($request->request->get('lastname'));
        }

        if ($request->request->has('email')) {
            $user->setEmail($request->request->get('email'));
        }

        if ($request->request->has('password')) {
            $user->setPlainPassword($request->request->get('password'));
        }

        if ($request->request->has('interfaceLanguage')) {
            $user->setInterfaceLanguage($request->request->get('interfaceLanguage'));
        }

        if ($request->request->has('theme')) {
            $user->setInterfaceLanguage($request->request->get('theme'));
        }

        $userManager = $this->get('phlexible_user.user_manager');
        $userManager->updateUser($user);

        return new ResultResponse(true, 'User updated.');
    }
}
