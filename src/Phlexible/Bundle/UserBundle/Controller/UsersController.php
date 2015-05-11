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
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Model\UserInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\UserBundle\Entity\User;
use Phlexible\Bundle\UserBundle\Form\Type\UserType;
use Phlexible\Bundle\UserBundle\Model\UserCriteriaBuilder;
use Phlexible\Bundle\UserBundle\UsersMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Users controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @Security("is_granted('ROLE_USERS')")
 * @Rest\NamePrefix("phlexible_api_user_")
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
     * @Rest\QueryParam(name="start", requirements="\d+", default=0, description="First result")
     * @Rest\QueryParam(name="limit", requirements="\d+", default=20, description="Max results")
     * @Rest\QueryParam(name="sort", requirements="\w+", default="username", description="Sort field")
     * @Rest\QueryParam(name="dir", requirements="\w+", default="ASC", description="Sort direction")
     * @Rest\QueryParam(name="criteria", description="Search criteria.")
     * @Rest\View
     * @ApiDoc(
     *   description="Returns a collection of User",
     *   section="user",
     *   resource=true,
     *   statusCodes={
     *     200="Returned when successful",
     *   }
     * )
     */
    public function getUsersAction(ParamFetcher $paramFetcher)
    {
        $start = $paramFetcher->get('start');
        $limit = $paramFetcher->get('limit');
        $sort = $paramFetcher->get('sort');
        $dir = $paramFetcher->get('dir');

        $userManager = $this->container->get('phlexible_user.user_manager');
        $criteria = $userManager->createCriteria();

        UserCriteriaBuilder::applyFromParams($paramFetcher, $criteria);

        $userResult = $userManager->query($criteria, array($sort => $dir), $limit, $start);

        $users = array();
        foreach ($userResult as $user) {
            $users[] = $user;
        }

        return array(
            'users' => $users,
            'count' => count($users)
        );
    }

    /**
     * Get user
     *
     * @param string $userId
     *
     * @return UserInterface
     *
     * @Security("is_granted('ROLE_USER_ADMIN_READ')")
     * @Rest\View(templateVar="user")
     * @ApiDoc(
     *   description="Returns a User",
     *   section="user",
     *   output="Phlexible\Bundle\UserBundle\Entity\User",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when user was not found"
     *   }
     * )
     */
    public function getUserAction($userId)
    {
        $userManager = $this->container->get('phlexible_user.user_manager');
        $user = $userManager->find($userId);

        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found');
        }

        return array(
            'user' => $user
        );
    }

    /**
     * Create user
     *
     * @param Request $request
     *
     * @return ResultResponse
     *
     * @Security("is_granted('ROLE_USER_ADMIN_CREATE')")
     * @Rest\QueryParam(name="optin", requirements="[01]", default=0, description="Optin")
     * @ApiDoc(
     *   description="Create a User",
     *   section="user",
     *   input="Phlexible\Bundle\UserBundle\Form\Type\UserType",
     *   statusCodes={
     *     201="Returned when user was created",
     *     204="Returned when user was updated",
     *     404="Returned when user was not found"
     *   }
     * )
     */
    public function postUsersAction(Request $request)
    {
        $userManager = $this->get('phlexible_user.user_manager');

        return $this->processForm($request, new User(), (bool) $request->get('optin'));

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('User "' . $user->getUsername() . '" created.'));
    }

    /**
     * Update user
     *
     * @param Request $request
     * @param string  $userId
     *
     * @return ResultResponse
     * @throws \Exception
     *
     * @Security("is_granted('ROLE_USER_ADMIN_UPDATE')")
     * @Rest\QueryParam(name="optin", requirements="[01]", default=0, description="Optin")
     * @ApiDoc(
     *   description="Update a User",
     *   section="user",
     *   input="Phlexible\Bundle\UserBundle\Form\Type\UserType",
     *   statusCodes={
     *     201="Returned when user was created",
     *     204="Returned when user was updated",
     *     404="Returned when user was not found"
     *   }
     * )
     */
    public function putUserAction(Request $request, $userId)
    {
        $userManager = $this->get('phlexible_user.user_manager');
        $user = $userManager->find($userId);

        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found');
        }

        return $this->processForm($request, new User(), (bool) $request->get('optin'));

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('User "' . $user->getUsername() . '" updated.'));
    }

    /**
     * @param Request       $request
     * @param UserInterface $user
     * @param bool          $optin
     *
     * @return View|Response
     */
    private function processForm(Request $request, UserInterface $user, $optin = false)
    {
        $statusCode = !$user->getId() ? 201 : 204;

        $form = $this->createForm(new UserType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $userManager = $this->get('phlexible_user.user_manager');

            if ($request->get('username') && $request->get('username') !== $user->getUsername()
                && $userManager->checkUsername($request->get('username'))) {
                throw new BadRequestHttpException('Username "' . $request->get('username') . '" already exists.');
            }
            if ($request->get('email') && $request->get('email') !== $user->getEmail()
                && $userManager->checkEmail($request->get('email'))) {
                throw new BadRequestHttpException('Email "' . $request->get('email') . '" already exists.');
            }

            if ($optin) {
                $user->setPasswordToken(Uuid::generate());

                $mailer = $this->get('phlexible_user.mailer');
                $mailer->sendNewAccountEmailMessage($user);
            }

            $userManager->updateUser($user);

            $response = new Response();
            $response->setStatusCode($statusCode);

            // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'phlexible_api_user_get_user', array('userId' => $user->getId()),
                        true // absolute
                    )
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }

    /**
     * Delete user
     *
     * @param Request $request
     * @param string  $userId
     *
     * @return Response
     * @Security("is_granted('ROLE_USER_ADMIN_DELETE')")
     * @Rest\View(statusCode=204)
     * @ApiDoc(
     *   description="Delete a User",
     *   section="user",
     *   statusCodes={
     *     204="Returned when successful",
     *     404="Returned when the user was not found"
     *   }
     * )
     */
    public function deleteUserAction(Request $request, $userId)
    {
        $userManager = $this->get('phlexible_user.user_manager');
        $user = $userManager->find($userId);

        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found');
        }

        $userManager->deleteUser($user);

        $this->get('phlexible_message.message_poster')
            ->post(UsersMessage::create('User "' . $user->getUsername() . '" deleted.'));
    }

    /**
     * Return roles for user
     *
     * @param int $userId
     *
     * @return JsonResponse
     * @Security("is_granted('ROLE_USER_ADMIN_READ')")
     * @ApiDoc(
     *   description="Returns a Users Roles",
     *   section="user",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when user was not found"
     *   }
     * )
     */
    public function getUserRolesAction($userId)
    {
        $userManager = $this->get('phlexible_user.user_manager');
        $user = $userManager->find($userId);

        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found');
        }

        $roleData = $this->getRoleData();
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
     * @ApiDoc(
     *   description="Returns a Users Groups",
     *   section="user",
     *   statusCodes={
     *     200="Returned when successful",
     *     404="Returned when user was not found"
     *   }
     * )
     */
    public function getUserGroupsAction($userId)
    {
        $userManager = $this->get('phlexible_user.user_manager');
        $user = $userManager->find($userId);

        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found');
        }

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
}
