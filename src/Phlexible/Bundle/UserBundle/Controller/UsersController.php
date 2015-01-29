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
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Model\UserInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\UserBundle\Entity\User;
use Phlexible\Bundle\UserBundle\Model\UserCriteriaBuilder;
use Phlexible\Bundle\UserBundle\UsersMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Users controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Security("is_granted('ROLE_USERS')")
 * @Prefix("/user")
 * @NamePrefix("phlexible_user_")
 */
class UsersController extends FOSRestController
{
    /**
     * Get users
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Security("is_granted('ROLE_USER_ADMIN_READ')")
     * @ApiDoc(
     *   filters={
     *      {"name"="start", "dataType"="integer", "description"="Start index", "default"=0},
     *      {"name"="limit", "dataType"="integer", "description"="Limit results", "default"=20},
     *      {"name"="sort", "dataType"="string", "description"="Sort field", "default"="username"},
     *      {"name"="dir", "dataType"="string", "description"="Sort direction", "default"="ASC"},
     *      {"name"="search", "dataType"="array", "description"="Search"}
     *   }
     * )
     */
    public function getUsersAction(Request $request)
    {
        $start = $request->get('start');
        $limit = $request->get('limit', 20);
        $sort = $request->get('sort', 'username');
        $dir = $request->get('dir', 'ASC');

        $userManager = $this->container->get('phlexible_user.user_manager');
        $userSerializer = $this->container->get('phlexible_user.user_serializer');
        $criteria = $userManager->createCriteria();

        $criteria
            ->orderBy(array($sort => $dir))
            ->setFirstResult($start)
            ->setMaxResults($limit);

        UserCriteriaBuilder::applyFromRequest($criteria, $request);

        $result = $userManager->query($criteria);

        $users = array();
        foreach ($result as $user) {
            $users[] = $userSerializer->serialize($user);
        }

        return $this->handleView($this->view(
            array(
                'users' => $users,
                'count' => count($result)
            )
        ));
    }

    /**
     * Get user
     *
     * @param string $userId
     *
     * @return UserInterface
     *
     * @Security("is_granted('ROLE_USER_ADMIN_READ')")
     * @View(templateVar="user")
     * @ApiDoc()
     */
    public function getUserAction($userId)
    {
        $userManager = $this->container->get('phlexible_user.user_manager');
        $user = $userManager->find($userId);

        return $user;
    }

    /**
     * Create user
     *
     * @param Request $request
     *
     * @throws \Exception
     * @return ResultResponse
     * @Security("is_granted('ROLE_USER_ADMIN_CREATE')")
     * @ApiDoc()
     */
    public function postUsersAction(Request $request)
    {
        $userManager = $this->get('phlexible_user.user_manager');

        if ($request->get('username') && $userManager->checkUsername($request->get('username'))) {
            throw new \Exception('Username "' . $request->get('username') . '" already exists.');
        }
        if ($request->get('email') && $userManager->checkEmail($request->get('email'))) {
            throw new \Exception('Email "' . $request->get('email') . '" already exists.');
        }

        $user = $userManager->createUser();

        $this->requestToUser($request, $user);

        $user
            ->setCreatedAt(new \DateTime())
            ->setModifiedAt(new \DateTime());

        $optin = (bool) $request->request->get('optin', false);
        if ($optin) {
            $user->setPasswordToken(Uuid::generate());

            $mailer = $this->get('phlexible_user.mailer');
            $mailer->sendNewAccountEmailMessage($user);
        }

        $userManager->updateUser($user);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('User "' . $user->getUsername() . '" created.'));

        return $this->handleView($this->view(
            array(
                'success' => true,
                'msg'     => "User {$user->getUsername()} created."
            )
        ));
    }

    /**
     * Update user
     *
     * @param Request $request
     * @param string  $userId
     *
     * @throws \Exception
     * @return ResultResponse
     * @Route("/{userId}", name="phlexible_user_update")
     * @Method("PUT")
     * @Security("is_granted('ROLE_USER_ADMIN_UPDATE')")
     * @ApiDoc(
     *   requirements={
     *     {"name"="username", "dataType"="string", "required"=true, "description"="Username"},
     *     {"name"="email", "dataType"="string", "required"=true, "description"="Email"},
     *     {"name"="password", "dataType"="string", "required"=false, "description"="password"},
     *     {"name"="firstname", "dataType"="string", "required"=true, "description"="Firstname"},
     *     {"name"="lastname", "dataType"="string", "required"=true, "description"="Lastname"},
     *     {"name"="roles", "dataType"="array", "required"=false, "description"="Roles"},
     *     {"name"="groups", "dataType"="array", "required"=false, "description"="Groups"},
     *     {"name"="property_*", "dataType"="string", "required"=false, "description"="Property"}
     *   }
     * )
     */
    public function putUserAction(Request $request, $userId)
    {
        $userManager = $this->get('phlexible_user.user_manager');

        $user = $userManager->find($userId);
        /* @var $user User */

        if ($request->get('username') && $request->get('username') !== $user->getUsername()
                && $userManager->checkUsername($request->get('username'))) {
            throw new \Exception('Username "' . $request->get('username') . '" already exists.');
        }
        if ($request->get('email') && $request->get('email') !== $user->getEmail()
                && $userManager->checkEmail($request->get('email'))) {
            throw new \Exception('Email "' . $request->get('email') . '" already exists.');
        }

        $this->requestToUser($request, $user);

        $user
            ->setModifiedAt(new \DateTime());

        $optin = (bool) $request->request->get('optin', false);
        if ($optin) {
            $user->setPasswordToken(Uuid::generate());

            $mailer = $this->get('phlexible_user.mailer');
            $mailer->sendNewPasswordEmailMessage($user);
        }

        $userManager->updateUser($user);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('User "' . $user->getUsername() . '" updated.'));

        return $this->handleView($this->view(
            array(
                'success' => true,
                'msg'     => "User {$user->getUsername()} updated."
            )
        ));
    }

    /**
     * Delete user
     *
     * @param Request $request
     * @param string  $userId
     *
     * @return ResultResponse
     * @Route("/{userId}", name="phlexible_user_delete")
     * @Method("DELETE")
     * @Security("is_granted('ROLE_USER_ADMIN_DELETE')")
     * @ApiDoc()
     */
    public function deleteUserAction(Request $request, $userId)
    {
        $successorUserId = $request->request->get('successor');

        $userManager = $this->get('phlexible_user.user_manager');

        $successorUser = $userManager->find($successorUserId);
        $user = $userManager->find($userId);

        $userManager->deleteUser($user, $successorUser);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('User "' . $user->getUsername() . '" deleted.'));

        return $this->handleView($this->view(
            array(
                'success' => true,
                'msg'     => "User {$user->getUsername()} deleted."
            )
        ));
    }

    /**
     * Return roles for user
     *
     * @param int $userId
     *
     * @return JsonResponse
     * @throws \Exception
     * @Security("is_granted('ROLE_USER_ADMIN_READ')")
     * @ApiDoc()
     */
    public function getUserRolesAction($userId)
    {
        $userManager = $this->get('phlexible_user.user_manager');

        $roleData = $this->getRoleData();
        $user = $userManager->findUserBy(array('id' => $userId));

        if (!$user) {
            throw new \Exception("User $userId not found");
        }

        $userRoles = $user->getRoles();

        foreach ($roleData as $key => $roleRow) {
            $roleData[$key]['member'] = in_array($roleRow['id'], $userRoles);
        }

        return $this->handleView($this->view(
            array(
                'roles' => $roleData
            )
        ));
    }

    /**
     * Return groups for user
     *
     * @param int $userId
     *
     * @return JsonResponse
     * @Security("is_granted('ROLE_USER_ADMIN_READ')")
     * @ApiDoc()
     */
    public function getUserGroupsAction($userId)
    {
        $groupData = $this->getGroupData();

        foreach ($groupData as $key => $groupRow) {
            $groupData['member'] = false;
        }


        return $this->handleView($this->view(
            array(
                'groups' => $groupData
            )
        ));
    }

    /**
     * @param array $roles
     *
     * @return array
     */
    private function resolveRolesHierarchy(array $roles)
    {
        $list = array();

        foreach ($roles as $roleKey => $role) {
            if (is_array($role)) {
                $list[] = $roleKey;
                $list = array_merge($list, $this->resolveRolesHierarchy($role));
            } else {
                $list[] = $role;
            }
        }

        return array_unique($list);
    }

    /**
     * @return array
     */
    private function getRoleData()
    {
        $rolesHierarchy = $this->container->getParameter('security.role_hierarchy.roles');
        $roles = $this->resolveRolesHierarchy($rolesHierarchy);

        $roleData = array();
        foreach ($roles as $role) {
            $roleData[] = array(
                'id'     => $role,
                'role'   => $role,
            );
        }

        return $roleData;
    }

    /**
     * @return array
     */
    private function getGroupData()
    {
        return array();
    }

    /**
     * @param Request $request
     * @param User    $user
     */
    private function requestToUser(Request $request, User $user)
    {
        if ($request->request->get('firstname')) {
            $user->setFirstname($request->get('firstname'));
        }
        if ($request->request->get('lastname')) {
            $user->setLastname($request->get('lastname'));
        }
        if ($request->request->get('email')) {
            $user->setEmail($request->get('email'));
        }
        if ($request->request->get('username')) {
            $user->setUsername($request->get('username'));
        }
        if ($request->request->get('comment')) {
            $user->setComment($request->get('comment'));
        }

        // password
        if ($request->request->get('password')) {
            $user->setPlainPassword($request->request->get('password'));
        }

        // expires
        if ($request->request->get('expires')) {
            $user->setExpiresAt(new \DateTime($request->get('expires')));
        } else {
            $user->setExpiresAt(null);
        }

        // properties
        $properties = [];
        foreach ($request->request->all() as $key => $value) {
            if (substr($key, 0, 9) === 'property_') {
                $key = substr($key, 9);
                $properties[$key] = $value;
            }
        }
        if (count($properties)) {
            $user->setProperties($properties);
        } else {
            $user->setProperties([]);
        }

        // roles
        $roles = $request->request->get('roles');
        if ($roles) {
            $user->setRoles(explode(',', $roles));
        } else {
            $user->setRoles([]);
        }

        // groups
        $groups = $request->request->get('groups');
        if ($groups) {
            $groupManager = $this->get('phlexible_user.group_manager');
            foreach (explode(',', $groups) as $groupId) {
                $group = $groupManager->find($groupId);
                $user->addGroup($group);
            }
        }
    }
}
