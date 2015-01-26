<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Options controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/users/options")
 */
class OptionsController extends Controller
{
    /**
     * Save details
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("", name="phlexible_options")
     * @Method("PATCH")
     */
    public function savedetailsAction(Request $request)
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
