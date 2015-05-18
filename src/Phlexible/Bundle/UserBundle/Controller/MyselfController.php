<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Myself controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Rest\NamePrefix("phlexible_api_user_")
 */
class MyselfController extends Controller
{
    /**
     * Get information about current user
     *
     * @return Response
     *
     * @Rest\View
     * @ApiDoc(
     *   description="Returns current User",
     *   section="user",
     *   resource=true,
     *   output="Phlexible\Bundle\UserBundle\Entity\User",
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
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
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Update current User",
     *   section="user",
     *   input="Phlexible\Bundle\UserBundle\Form\Type\UserType",
     *   statusCodes={
     *     204="Returned when user was updated",
     *   }
     * )
     */
    public function putMyselfAction(Request $request)
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
    }
}
