<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\UserBundle\UsersMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Groups controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/users/groups")
 * @Security("is_granted('ROLE_GROUPS')")
 */
class GroupsController extends Controller
{
    /**
     * List groups
     *
     * @return JsonResponse
     * @Route("", name="phlexible_groups")
     * @Method("GET")
     * @Security("is_granted('ROLE_GROUP_ADMIN_READ')")
     * @ApiDoc(
     *   description="Returns a list of groups."
     * )
     */
    public function listAction()
    {
        $groupManager = $this->get('phlexible_user.group_manager');

        $groups = [];
        foreach ($groupManager->findAll() as $group) {
            $members = [];
            foreach ($group->getUsers() as $user) {
                $members[] = $user->getDisplayName();
            }
            sort($members);

            $groups[] = [
                'gid'       => $group->getId(),
                'name'      => $group->getName(),
                'readonly'  => false,
                'memberCnt' => count($members),
                'members'   => $members
            ];
        }

        return new JsonResponse($groups);
    }

    /**
     * Create new group
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="phlexible_group_create")
     * @Method("POST")
     * @Security("is_granted('ROLE_GROUP_ADMIN_CREATE')")
     * @ApiDoc(
     *   description="Create new group.",
     *   requirements={
     *     {"name"="name", "dataType"="string", "required"=true, "description"="New group name"}
     *   }
     * )
     */
    public function createAction(Request $request)
    {
        $name = $request->get('name');

        $groupManager = $this->get('phlexible_user.group_manager');

        $group = $groupManager->create()
            ->setName($name)
            ->setCreateUserId($this->getUser()->getId())
            ->setCreatedAt(new \DateTime())
            ->setModifyUserId($this->getUser()->getId())
            ->setModifiedAt(new \DateTime());

        $groupManager->updateGroup($group);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('Group "' . $group->getName() . '" created.'));

        return new ResultResponse(true, "Group $name created.");
    }

    /**
     * Update group
     *
     * @param Request $request
     * @param string  $groupId
     *
     * @return JsonResponse
     * @Route("/{groupId}", name="phlexible_group_update")
     * @Method("PATCH")
     * @Security("is_granted('ROLE_GROUP_ADMIN_UPDATE')")
     * @ApiDoc(
     *   description="Update group.",
     *   requirements={
     *     {"name"="name", "dataType"="string", "required"=true, "description"="Name"}
     *   }
     * )
     */
    public function patchAction(Request $request, $groupId)
    {
        $name = $request->get('name');

        $groupManager = $this->get('phlexible_user.group_manager');

        $group = $groupManager->find($groupId);
        $oldName = $group->getName();
        $group
            ->setName($name)
            ->setModifyUserId($this->getUser()->getId())
            ->setModifiedAt(new \DateTime());

        $groupManager->updateGroup($group);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('Group "' . $group->getName() . '" updated.'));

        return new ResultResponse(true, "Group $oldName renamed to $name.");
    }

    /**
     * Delete group
     *
     * @param string $groupId
     *
     * @return JsonResponse
     * @Route("/{groupId}", name="phlexible_group_delete")
     * @Method("DELETE")
     * @Security("is_granted('ROLE_GROUP_ADMIN_DELETE')")
     * @ApiDoc(
     *   description="Delete group."
     * )
     */
    public function deleteAction($groupId)
    {
        $groupManager = $this->get('phlexible_user.group_manager');

        $group = $groupManager->find($groupId);
        $name = $group->getName();

        $groupManager->deleteGroup($group);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('Group "' . $group->getName() . '" deleted.'));

        return new ResultResponse(true, "Group $name deleted.");
    }
}
