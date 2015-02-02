<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Prefix;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\UserBundle\Entity\Group;
use Phlexible\Bundle\UserBundle\UsersMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @ApiDoc
     */
    public function getGroupsAction()
    {
        $groupManager = $this->get('phlexible_user.group_manager');

        $groups = $groupManager->findAll();

        return $this->handleView($this->view(
            array(
                'groups' => $groups,
                'count'  => count($groups)
            )
        ));
    }

    /**
     * Get group
     *
     * @param string $groupId
     *
     * @return Response
     *
     * @Security("is_granted('ROLE_GROUP_ADMIN_READ')")
     * @View(templateVar="group")
     * @ApiDoc
     */
    public function getGroupAction($groupId)
    {
        $groupManager = $this->get('phlexible_user.group_manager');

        $group = $groupManager->find($groupId);

        return $this->handleView($this->view($group));
    }

    /**
     * Create new group
     *
     * @param Group $group
     *
     * @return Response
     *
     * @Security("is_granted('ROLE_GROUP_ADMIN_CREATE')")
     * @ParamConverter("group", converter="fos_rest.request_body")
     * @Post("/groups")
     * @ApiDoc()
     */
    public function postGroupsAction(Group $group)
    {
        $groupManager = $this->get('phlexible_user.group_manager');

        $groupManager->updateGroup($group);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('Group "' . $group->getName() . '" created.'));

        return $this->handleView($this->view(
            array(
                'success' => true,
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
     * @ApiDoc
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
     * @ApiDoc
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
     * @ApiDoc
     */
    public function deleteGroupAction($groupId)
    {
        $groupManager = $this->get('phlexible_user.group_manager');

        $group = $groupManager->find($groupId);

        $groupManager->deleteGroup($group);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('Group "' . $group->getName() . '" deleted.'));

        return $this->handleView($this->view(
            array(
                'success' => true,
            )
        ));
    }
}
