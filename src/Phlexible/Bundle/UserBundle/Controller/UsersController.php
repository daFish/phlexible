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
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\UserBundle\Entity\User;
use Phlexible\Bundle\UserBundle\UsersMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Users controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/users")
 * @Security("is_granted('ROLE_USERS')")
 */
class UsersController extends Controller
{
    /**
     * List users
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="phlexible_users")
     * @Method("GET")
     * @Security("is_granted('ROLE_USER_ADMIN_READ')")
     * @ApiDoc(
     *   description="Returns a list of users.",
     *   filters={
     *      {"name"="start", "dataType"="integer", "description"="Start index", "default"=0},
     *      {"name"="limit", "dataType"="integer", "description"="Limit results", "default"=20},
     *      {"name"="sort", "dataType"="string", "description"="Sort field", "default"="username"},
     *      {"name"="dir", "dataType"="string", "description"="Sort direction", "default"="ASC"},
     *      {"name"="search", "dataType"="array", "description"="Search"}
     *   }
     * )
     */
    public function listAction(Request $request)
    {
        $start = $request->get('start');
        $limit = $request->get('limit', 20);
        $sort = $request->get('sort', 'username');
        $dir = $request->get('dir', 'ASC');
        $search = $request->get('search', null);

        $userManager = $this->container->get('phlexible_user.user_manager');
        $userSerializer = $this->container->get('phlexible_user.user_serializer');
        $userQuery = $userManager->query();

        $userQuery
            ->sort($sort, $dir)
            ->limit($start, $limit);

        if ($search !== null) {
            $search = json_decode($search, true);

            foreach ($search as $key => $value) {
                if (!$value) {
                    continue;
                }

                if ($key == 'key') {
                    $userQuery->byValue($value);
                } elseif ($key == 'account_disabled') {
                    $userQuery->byAccountDisabled();
                } elseif ($key == 'account_expired') {
                    $userQuery->byAccountExpired();
                } elseif ($key == 'account_has_expire_date') {
                    $userQuery->byAccountHasExpireDate();
                } elseif ($key === 'roles') {
                    foreach ($value as $role) {
                        $userQuery->byRole($role);
                    }
                } elseif (substr($key, 0, 5) == 'role_') {
                    $userQuery->byRole(substr($key, 5));
                } elseif (substr($key, 0, 6) == 'group_') {
                    $userQuery->byGroup(substr($key, 6));
                }
            }
        }

        $users = array();
        foreach ($userQuery->getResult() as $user) {
            $users[] = $userSerializer->serialize($user);
        }

        return new JsonResponse(
            array(
                'users' => $users,
                'count' => count($userQuery)
            )
        );
    }

    /**
     * User details
     *
     * @param Request $request
     * @param string  $userId
     *
     * @return JsonResponse
     * @Route("/{userId}", name="phlexible_user")
     * @Method("GET")
     * @Security("is_granted('ROLE_USER_ADMIN_READ')")
     * @ApiDoc(
     *   description="Returns a single user."
     * )
     */
    public function detailsAction(Request $request, $userId)
    {
        $userManager = $this->container->get('phlexible_user.user_manager');
        $userSerializer = $this->container->get('phlexible_user.user_serializer');
        $user = $userManager->find($userId);

        return new JsonResponse($userSerializer->serialize($user));
    }

    /**
     * Create user
     *
     * @param Request $request
     *
     * @throws \Exception
     * @return ResultResponse
     * @Route("/create", name="phlexible_user_create")
     * @Method("POST")
     * @Security("is_granted('ROLE_USER_ADMIN_CREATE')")
     * @ApiDoc(
     *   description="Create user.",
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
    public function createAction(Request $request)
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

        return new ResultResponse(true, "User {$user->getUsername()} created.");
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
     *   description="Update user.",
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
    public function updateAction(Request $request, $userId)
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

        return new ResultResponse(true, "User {$user->getUsername()} updated.");
    }

    /**
     * Delete users
     *
     * @param Request $request
     * @param string  $userId
     *
     * @return ResultResponse
     * @Route("/{userId}", name="phlexible_user_delete")
     * @Method("DELETE")
     * @Security("is_granted('ROLE_USER_ADMIN_DELETE')")
     * @ApiDoc(
     *   description="Delete user."
     * )
     */
    public function deleteAction(Request $request, $userId)
    {
        $successorUserId = $request->request->get('successor');

        $userManager = $this->get('phlexible_user.user_manager');

        $successorUser = $userManager->find($successorUserId);
        $user = $userManager->find($userId);

        $userManager->deleteUser($user, $successorUser);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('User "' . $user->getUsername() . '" deleted.'));

        return new ResultResponse(true);
    }

    /**
     * Return available roles by user
     *
     * @param int $userId
     *
     * @return JsonResponse
     * @throws \Exception
     * @Route("/{userId}/roles", name="phlexible_user_roles", options={"expose"=true})
     * @Method("GET")
     * @Security("is_granted('ROLE_USER_ADMIN_READ')")
     * @ApiDoc(
     *   description="Returns roles for user."
     * )
     */
    public function userRolesAction($userId)
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

        return new JsonResponse(array('roles' => $roleData));
    }

    /**
     * Return available Groups
     *
     * @param int $userId
     *
     * @return JsonResponse
     * @Route("/{userId}/groups", name="phlexible_user_groups", options={"expose"=true})
     * @Method("GET")
     * @Security("is_granted('ROLE_USER_ADMIN_READ')")
     * @ApiDoc(
     *   description="Returns groups for user."
     * )
     */
    public function userGroupsAction($userId)
    {
        $groupData = $this->getGroupData();

        foreach ($groupData as $key => $groupRow) {
            $groupData['member'] = false;
        }

        return new JsonResponse(array('groups' => $groupData));
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
