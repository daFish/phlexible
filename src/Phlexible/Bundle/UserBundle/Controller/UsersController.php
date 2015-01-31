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
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\UserBundle\Model\UserInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\UserBundle\Entity\User;
use Phlexible\Bundle\UserBundle\UsersMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Users controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_USERS')")
 * @Prefix("/user")
 * @NamePrefix("phlexible_user_")
 */
class UsersController extends FOSRestController
{
    /**
     * Get users
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     *
     * @Security("is_granted('ROLE_USER_ADMIN_READ')")
     * @QueryParam(name="start", requirements="\d+", default=0, description="First result")
     * @QueryParam(name="limit", requirements="\d+", default=20, description="Max results")
     * @QueryParam(name="sort", requirements="\w+", default="username", description="Sort field")
     * @QueryParam(name="dir", requirements="\w+", default="ASC", description="Sort direction")
     * @QueryParam(name="criteria", description="Search criteria.")
     * @ApiDoc()
     */
    public function getUsersAction(ParamFetcher $paramFetcher)
    {
        $start = $paramFetcher->get('start');
        $limit = $paramFetcher->get('limit');
        $sort = $paramFetcher->get('sort');
        $dir = $paramFetcher->get('dir');
        $criteriaString = $paramFetcher->get('criteria');

        $userManager = $this->container->get('phlexible_user.user_manager');
        $criteria = $userManager->createCriteria();

        $criteria
            ->orderBy(array($sort => $dir))
            ->setFirstResult($start)
            ->setMaxResults($limit);

        //UserCriteriaBuilder::applyFromRequest($criteria, $criteriaString);

        $userResult = $userManager->query($criteria);

        $users = array();
        foreach ($userResult as $user) {
            $users[] = $user;
        }

        return $this->handleView($this->view(
            array(
                'users' => $users,
                'count' => count($users)
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
     * @param User         $user
     * @param ParamFetcher $paramFetcher
     *
     * @return ResultResponse
     *
     * @throws \Exception
     * @Security("is_granted('ROLE_USER_ADMIN_CREATE')")
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @QueryParam(name="optin", requirements="[01]", default=0, description="Optin")
     * @Post("/users")
     * @ApiDoc()
     */
    public function postUsersAction(User $user, ParamFetcher $paramFetcher)
    {
        $userManager = $this->get('phlexible_user.user_manager');

        if ($userManager->checkUsername($user->getUsername())) {
            throw new \Exception('Username "' . $user->getUsername() . '" already exists.');
        }
        if ($userManager->checkEmail($user->getEmail())) {
            throw new \Exception('Email "' . $user->getEmail() . '" already exists.');
        }

        $user
            ->setCreatedAt(new \DateTime())
            ->setModifiedAt(new \DateTime());

        $optin = $paramFetcher->get('optin');
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
            )
        ));
    }

    /**
     * Update user
     *
     * @param User         $user
     * @param ParamFetcher $paramFetcher
     * @param string       $userId
     *
     * @return ResultResponse
     * @throws \Exception
     *
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @QueryParam(name="optin", requirements="[01]", default=0, description="Optin")
     * @Security("is_granted('ROLE_USER_ADMIN_UPDATE')")
     * @Put("/users/{userId}")
     * @ApiDoc()
     */
    public function putUserAction(User $user, ParamFetcher $paramFetcher, $userId)
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
     * @return Response
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
     * Set successor
     *
     * @param Request $request
     * @param string  $userId
     *
     * @return Response
     *
     * @ApiDoc(
     *   requirements={
     *     {"name"="successorUserId", "dataType"="string", "required"=true, "description"="Successor user ID"},
     *   }
     * )
     */
    public function successorUserAction(Request $request, $userId)
    {
        $successorUserId = $request->get('successorUserId');

        $userManager = $this->get('phlexible_user.user_manager');
        $user = $userManager->find($userId);
        $successorUser = $userManager->find($successorUserId);

        $successor = $this->get('phlexible_user.successor_service');
        $successor->set($user, $successorUser);

        return new ResultResponse(true, 'Successor set');
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
