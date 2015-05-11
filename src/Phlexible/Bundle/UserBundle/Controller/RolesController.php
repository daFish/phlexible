<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Roles controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_USERS')")
 * @Rest\Prefix("/user")
 * @Rest\NamePrefix("phlexible_api_user_")
 */
class RolesController extends FOSRestController
{
    /**
     * Get roles
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Returns a collection of Role",
     *   section="user",
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getRolesAction()
    {
        $roleHierarchy = $this->container->getParameter('security.role_hierarchy.roles');

        $roles = [];
        foreach (array_keys($roleHierarchy) as $role) {
            $roles[] = [
                'id' => $role,
                'role' => $role
            ];
        }

        return $this->handleView($this->view(
            array(
                'roles' => $roles,
                'count' => count($roles)
            )
        ));
    }
}
