<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\UserBundle\UsersMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Groups controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Security("is_granted('ROLE_GROUPS')")
 * @Prefix("/user")
 * @NamePrefix("phlexible_user_")
 */
class GroupsController extends FOSRestController
{
    /**
     * Get groups
     *
     * @return Response
     *
     * @Security("is_granted('ROLE_GROUP_ADMIN_READ')")
     * @ApiDoc(
     *   description="Returns a list of groups."
     * )
     */
    public function getGroupsAction()
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

        return $this->handleView($this->view(
            array(
                'groups' => $groups,
                'count'  => count($groups)
            )
        ));
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
     *   requirements={
     *     {"name"="name", "dataType"="string", "required"=true, "description"="New group name"}
     *   }
     * )
     */
    public function postGroupsAction(Request $request)
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

        return $this->handleView($this->view(
            array(
                'success' => true,
                'message' => "Group $name created."
            )
        ));
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
     *   requirements={
     *     {"name"="name", "dataType"="string", "required"=true, "description"="New group name"}
     *   }
     * )
     */
    public function putGroupAction(Request $request, $groupId)
    {
        $name = $request->get('name');

        $groupManager = $this->get('phlexible_user.group_manager');

        $group = $groupManager->find($groupId)
            ->setName($name)
            ->setCreateUserId($this->getUser()->getId())
            ->setCreatedAt(new \DateTime())
            ->setModifyUserId($this->getUser()->getId())
            ->setModifiedAt(new \DateTime());

        $groupManager->updateGroup($group);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('Group "' . $group->getName() . '" created.'));

        return $this->handleView($this->view(
            array(
                'success' => true,
                'message' => "Group $name created."
            )
        ));
    }

    /**
     * Update group
     *
     * @param Request $request
     * @param string  $groupId
     *
     * @return Response
     * @Security("is_granted('ROLE_GROUP_ADMIN_UPDATE')")
     * @ApiDoc(
     *   requirements={
     *     {"name"="name", "dataType"="string", "required"=true, "description"="Name"}
     *   }
     * )
     */
    public function patchGroupAction(Request $request, $groupId)
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

        return $this->handleView($this->view(
            array(
                'success' => true,
                'message' => "Group $name patched."
            )
        ));
    }

    /**
     * Delete group
     *
     * @param string $groupId
     *
     * @return Response
     *
     * @Security("is_granted('ROLE_GROUP_ADMIN_DELETE')")
     * @ApiDoc()
     */
    public function deleteAction($groupId)
    {
        $groupManager = $this->get('phlexible_user.group_manager');

        $group = $groupManager->find($groupId);
        $name = $group->getName();

        $groupManager->deleteGroup($group);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('Group "' . $group->getName() . '" deleted.'));

        return $this->handleView($this->view(
            array(
                'success' => true,
                'message' => "Group $name patched."
            )
        ));
    }
}
