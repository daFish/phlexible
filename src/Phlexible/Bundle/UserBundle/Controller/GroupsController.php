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
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\UserBundle\Entity\Group;
use Phlexible\Bundle\UserBundle\Form\Type\GroupType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Groups controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Security("is_granted('ROLE_GROUPS')")
 * @Rest\NamePrefix("phlexible_api_user_")
 */
class GroupsController extends FOSRestController
{
    /**
     * Get groups
     *
     * @return Response
     *
     * @Security("is_granted('ROLE_GROUP_ADMIN_READ')")
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of Group",
     *   section="user",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getGroupsAction()
    {
        $groupManager = $this->get('phlexible_user.group_manager');
        $groups = $groupManager->findAll();

        return array(
            'groups' => $groups,
            'count'  => count($groups)
        );
    }

    /**
     * Get group
     *
     * @param string $groupId
     *
     * @return Response
     *
     * @Security("is_granted('ROLE_GROUP_ADMIN_READ')")
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a Group",
     *   section="user",
     *   output="Phlexible\Bundle\UserBundle\Entity\Group",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when group was not found"
     *   }
     * )
     */
    public function getGroupAction($groupId)
    {
        $groupManager = $this->get('phlexible_user.group_manager');
        $group = $groupManager->find($groupId);

        if (!$group instanceof Group) {
            throw new NotFoundHttpException('Group not found');
        }

        return array(
            'group' => $group
        );
    }

    /**
     * Create new group
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Security("is_granted('ROLE_GROUP_ADMIN_CREATE')")
     * @ApiDoc(
     *   description="Create a Group",
     *   section="user",
     *   input="Phlexible\Bundle\UserBundle\Form\Type\GroupType",
     *   statusCodes={
     *     201="Returned when group was created",
     *     204="Returned when group was updated",
     *     404="Returned when group was not found"
     *   }
     * )
     */
    public function postGroupsAction(Request $request)
    {
        return $this->processForm($request, new Group());
    }

    /**
     * Create new group
     *
     * @param Request $request
     * @param string  $groupId
     *
     * @return Response
     *
     * @Security("is_granted('ROLE_GROUP_ADMIN_UPDATE')")
     * @ApiDoc(
     *   description="Update a Group",
     *   section="user",
     *   input="Phlexible\Bundle\UserBundle\Form\Type\GroupType",
     *   statusCodes={
     *     201="Returned when group was created",
     *     204="Returned when group was updated",
     *     404="Returned when group was not found"
     *   }
     * )
     */
    public function putGroupAction(Request $request, $groupId)
    {
        $groupManager = $this->get('phlexible_user.group_manager');
        $group = $groupManager->find($groupId);

        if (!$group instanceof Group) {
            throw new NotFoundHttpException('Group not found');
        }

        return $this->processForm($request, $group);
    }

    /**
     * @param Request $request
     * @param Group   $group
     *
     * @return View|Response
     */
    private function processForm(Request $request, Group $group)
    {
        $statusCode = !$group->getId() ? 201 : 204;

        $form = $this->createForm(new GroupType(), $group);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $groupManager = $this->get('phlexible_user.group_manager');
            $groupManager->updateGroup($group);

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set(
                    'Location',
                    $this->generateUrl('phlexible_api_user_get_group', array('groupId' => $group->getId()), true)
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }

    /**
     * Delete group
     *
     * @param string $groupId
     *
     * @return Response
     *
     * @Security("is_granted('ROLE_GROUP_ADMIN_DELETE')")
     * @ApiDoc(
     *   description="Delete a Group",
     *   section="user",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the group was not found"
     *   }
     * )
     */
    public function deleteGroupAction($groupId)
    {
        $groupManager = $this->get('phlexible_user.group_manager');
        $group = $groupManager->find($groupId);

        if (!$group instanceof Group) {
            throw new NotFoundHttpException('Group not found');
        }

        $groupManager->deleteGroup($group);
    }
}
