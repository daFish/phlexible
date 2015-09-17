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

use FOS\UserBundle\Controller\ResettingController as BaseResettingController;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller managing the resetting of the password.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ResettingController extends BaseResettingController
{
    /**
     * Request reset user password: show form.
     *
     * @return Response
     * @Route("/resetting/request", name="fos_user_resetting_request")
     */
    public function requestAction()
    {
        return $this->container->get('templating')->renderResponse(
            'PhlexibleUserBundle:Resetting:request.html.'.$this->getEngine()
        );
    }

    /**
     * Request reset user password: submit form and send email.
     *
     * @return Response
     * @Route("/resetting/send-email", name="fos_user_resetting_send_email")
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->get('username');

        /** @var $user UserInterface */
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return $this->container->get('templating')->renderResponse(
                'PhlexibleUserBundle:Resetting:request.html.'.$this->getEngine(),
                array('invalid_username' => $username)
            );
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return $this->container->get('templating')->renderResponse(
                'PhlexibleUserBundle:Resetting:passwordAlreadyRequested.html.'.$this->getEngine()
            );
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->container->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));
        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_check_email'));
    }

    /**
     * Tell the user to check his email provider.
     *
     * @return Response
     * @Route("/resetting/check-email", name="fos_user_resetting_check_email")
     */
    public function checkEmailAction(Request $request)
    {
        $session = $request->getSession();
        $email = $session->get(static::SESSION_EMAIL);
        $session->remove(static::SESSION_EMAIL);

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        return $this->container->get('templating')->renderResponse(
            'PhlexibleUserBundle:Resetting:checkEmail.html.'.$this->getEngine(),
            array(
                'email' => $email,
            )
        );
    }

    /**
     * Reset user password.
     *
     * @return Response
     * @Route("/resetting/reset/{token}", name="fos_user_resetting_reset")
     */
    public function resetAction(Request $request, $token)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(
                sprintf('The user with "confirmation token" does not exist for value "%s"', $token)
            );
        }

        if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        $form = $this->container->get('fos_user.resetting.form');
        $formHandler = $this->container->get('fos_user.resetting.form.handler');
        $process = $formHandler->process($user);

        if ($process) {
            $this->setFlash('fos_user_success', 'resetting.flash.success');
            $response = new RedirectResponse($this->getRedirectionUrl($user));
            $this->authenticateUser($user, $response);

            return $response;
        }

        return $this->container->get('templating')->renderResponse(
            'PhlexibleUserBundle:Resetting:reset.html.'.$this->getEngine(),
            array(
                'token' => $token,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * Generate the redirection url when the resetting is completed.
     *
     * @param UserInterface $user
     *
     * @return string
     */
    protected function getRedirectionUrl(UserInterface $user)
    {
        return $this->container->get('router')->generate('phlexible_gui');
    }
}
