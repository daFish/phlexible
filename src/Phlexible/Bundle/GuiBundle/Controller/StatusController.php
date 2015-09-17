<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Status controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/gui/status")
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 */
class StatusController extends Controller
{
    /**
     * List status actions.
     *
     * @return Response
     * @Route("", name="phlexible_gui_status")
     */
    public function indexAction()
    {
        $output = '';
        $output .= '<a href="'.$this->generateUrl('gui_status_listeners').'">listeners</a><br/>';
        $output .= '<a href="'.$this->generateUrl('gui_status_versions').'">versions</a><br/>';
        $output .= '<a href="'.$this->generateUrl('gui_status_php').'">php</a><br/>';
        $output .= '<a href="'.$this->generateUrl('gui_status_load').'">load</a><br/>';
        $output .= '<a href="'.$this->generateUrl('gui_status_context').'">context</a><br/>';
        $output .= '<a href="'.$this->generateUrl('gui_status_session').'">session</a><br/>';

        return new Response($output);
    }

    /**
     * Show events.
     *
     * @return Response
     * @Route("/listeners", name="phlexible_gui_status_listeners")
     */
    public function listenersAction()
    {
        $dispatcher = $this->get('event_dispatcher');

        $listenerNames = array_keys($dispatcher->getListeners());
        sort($listenerNames);

        $output = '<pre>';
        $output .= str_repeat('=', 3).str_pad(' Events / Listeners ', 80, '=').PHP_EOL.PHP_EOL;

        foreach ($listenerNames as $listenerName) {
            $listeners = $dispatcher->getListeners($listenerName);

            $output .= $listenerName.
                ' (<a href="#'.$listenerName.'">'.count($listeners).' listeners</a>)'.PHP_EOL;
        }

        foreach ($listenerNames as $listenerName) {
            $listeners = $dispatcher->getListeners($listenerName);

            if (!$listenerName) {
                $listenerName = '(global)';
            }

            $output .= PHP_EOL.PHP_EOL.str_repeat('-', 3).'<a name="'.$listenerName.'"></a>'
                .str_pad(' '.$listenerName.' ', 80, '-').PHP_EOL.PHP_EOL;

            foreach ($listeners as $listener) {
                if (is_array($listener)) {
                    if (is_object($listener[0])) {
                        $listener = get_class($listener[0]).'->'.$listener[1].'()';
                    } else {
                        $listener = implode('::', $listener).'()';
                    }
                }
                $output .= '* '.$listener.PHP_EOL;
            }
        }

        return new Response($output);
    }

    /**
     * phpinfo.
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/php", name="phlexible_gui_status_php")
     */
    public function phpAction(Request $request)
    {
        $show = $request->query->get('show', -1);

        ob_start();
        phpinfo($show);
        $output = ob_get_clean();

        return new Response($output);
    }

    /**
     * Show versions.
     *
     * @return Response
     * @Route("/versions", name="phlexible_gui_status_versions")
     */
    public function versionsAction()
    {
        $output = '';
        $output .= '<div>PHP: '.PHP_VERSION.'</div>';
        $output .= '<div>phlexible: 1.0.0</div>';
        $output .= '<div>Symfony: '.\Symfony\Component\HttpKernel\Kernel::VERSION.'</div>';
        $output .= '<div>Doctrine DBAL: '.\Doctrine\DBAL\Version::VERSION.'</div>';
        $output .= '<div>Doctrine ORM: '.\Doctrine\ORM\Version::VERSION.'</div>';

        return new Response($output);
    }

    /**
     * Show load.
     *
     * @return Response
     * @Route("/load", name="phlexible_gui_status_load")
     */
    public function loadAction()
    {
        $output = json_encode(sys_getloadavg());

        return new Response($output);
    }

    /**
     * Show security context.
     *
     * @return Response
     * @Route("/context", name="phlexible_gui_status_context")
     */
    public function contextAction()
    {
        $tokenStorage = $this->get('security.token_storage');

        $token = $tokenStorage->getToken();
        $user = $token->getUser();

        $output = '<pre>';
        $output .= 'Token class: '.get_class($token).PHP_EOL;
        $output .= 'User class:  '.(is_object($user) ? get_class($user) : $user).PHP_EOL;
        $output .= PHP_EOL;
        $output .= 'Token username: ';
        $output .= print_r($token->getUsername(), 1).PHP_EOL;
        $output .= 'Token attributes: ';
        $output .= print_r($token->getAttributes(), 1).PHP_EOL;
        $output .= 'Token credentials: ';
        $output .= print_r($token->getCredentials(), 1).PHP_EOL;
        $output .= 'Token roles: ';
        $output .= print_r($token->getRoles(), 1).PHP_EOL;

        return new Response($output);
    }

    /**
     * Show session.
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/session", name="phlexible_gui_status_session")
     */
    public function sessionAction(Request $request)
    {
        $output = '<pre>';
        $output .= 'Security session namespace:'.PHP_EOL;
        $output .= '<ul>';
        foreach ($request->getSession()->all() as $key => $value) {
            if (is_object($value)) {
                $o = get_class($value);
            } elseif (is_array($value)) {
                $o = 'array '.count($value);
            } else {
                $o = $value;
                if (@unserialize($o)) {
                    $o = unserialize($o);
                }
            }
            $output .= '<li>'.$key.': '.$o.'</li>';
        }
        $output .= '</ul>';

        return new Response($output);
    }
}
