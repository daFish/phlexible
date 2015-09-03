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

use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;

/**
 * Security controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SecurityController extends BaseSecurityController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/login", name="fos_user_security_login")
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            // TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
            $error = $error->getMessage();
        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(Security::LAST_USERNAME);

        $csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');

        return $this->renderLogin(array(
                'last_username' => $lastUsername,
                'error'         => $error,
                'csrf_token' => $csrfToken,
            ));
    }

    /**
     * @return Response
     * @Route("/login_check", name="fos_user_security_check")
     */
    public function checkAction()
    {
        throw new \RuntimeException(
            'You must configure the check path to be handled by the firewall using form_login in your security ' .
            'firewall configuration.'
        );
    }

    /**
     * @return Response
     * @Route("/logout", name="fos_user_security_logout")
     */
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    /**
     * {@inheritdoc}
     */
    protected function renderLogin(array $data)
    {
        $template = sprintf(
            'PhlexibleUserBundle:Security:login.html.%s',
            $this->container->getParameter('fos_user.template.engine')
        );

        return $this->container->get('templating')->renderResponse($template, $data);
    }
}
