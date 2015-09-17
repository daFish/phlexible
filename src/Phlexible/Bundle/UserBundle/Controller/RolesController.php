<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Roles controller.
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
     * Get roles.
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Returns a collection of Role",
     *   section="user",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getRolesAction()
    {
        $roleHierarchy = $this->container->getParameter('security.role_hierarchy.roles');

        $roles = array();
        foreach (array_keys($roleHierarchy) as $role) {
            $roles[] = array(
                'id' => $role,
                'role' => $role,
            );
        }

        return $this->handleView($this->view(
            array(
                'roles' => $roles,
                'count' => count($roles),
            )
        ));
    }
}
