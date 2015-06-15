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
use Symfony\Component\Routing\Route as RoutingRoute;

/**
 * Status controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/gui/status")
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 */
class StatusController extends Controller
{
    /**
     * List status actions
     *
     * @return Response
     * @Route("", name="phlexible_gui_status")
     */
    public function indexAction()
    {
        $output = '';
        $output .= '<a href="'.$this->generateUrl('phlexible_gui_status_callbacks').'">Callbacks (deprecated)</a><br/>';
        $output .= '<a href="'.$this->generateUrl('phlexible_gui_status_listeners').'">Listeners</a><br/>';
        $output .= '<a href="'.$this->generateUrl('phlexible_gui_status_routes').'">Routes</a><br/>';
        $output .= '<a href="'.$this->generateUrl('phlexible_gui_status_php').'">PHP</a><br/>';
        $output .= '<a href="'.$this->generateUrl('phlexible_gui_status_versions').'">Versions</a><br/>';
        $output .= '<a href="'.$this->generateUrl('phlexible_gui_status_load').'">Load</a><br/>';
        $output .= '<a href="' . $this->generateUrl('phlexible_gui_status_context') . '">Context</a><br />';
        $output .= '<a href="' . $this->generateUrl('phlexible_gui_status_session') . '">Session</a>';

        return new Response($output);
    }

    /**
     * Show callbacks
     *
     * @return Response
     * @Route("/callbacks", name="phlexible_gui_status_callbacks")
     */
    public function callbacksAction()
    {
        $components = $this->container->getParameter('kernel.bundles');

        $allCallbacks = [];
        $out = '';

        foreach ($components as $id => $class) {
            $cur = str_repeat('-', 3).'<a name="'.$id.'">'.str_pad(' '.$id.' ', 20, '-').'</a>'.str_repeat('-', 70).PHP_EOL.PHP_EOL;

            $callbacks = get_class_methods($class);
            $reflection = new \ReflectionClass('Symfony\Component\HttpKernel\Bundle\Bundle');
            $methods = $reflection->getMethods();
            $nonCallbacks = ['__construct', 'initContainer', 'setCallbacks', 'setDependencies', 'setDescription', 'setName', 'setOrder', 'setPath', 'getFile', 'setFile', 'getPath', 'getContainer', 'setContainer', 'getControllerDirectory', 'setControllerDirectory', 'init'];
            foreach ($methods as $method) {
                $nonCallbacks[] = $method->getName();
            }
            $callbacks = array_diff($callbacks, $nonCallbacks);

            if (!count($callbacks)) {
                continue;
            }

            foreach ($callbacks as $callback) {
                if (!isset($allCallbacks[$callback])) {
                    $allCallbacks[$callback] = 0;
                }
                $allCallbacks[$callback]++;

                $url = $this->generateUrl('gui_status_callback', ['callback' => $callback, 'component' => $id]);
                $cur .= "<a href='$url'>$id::$callback()</a>" . PHP_EOL;
            }

            $out .= $cur . PHP_EOL;
        }

        ksort($allCallbacks);

        $output = "<pre>" . str_repeat('=', 3).' Callbacks '.str_repeat('=', 80).PHP_EOL.PHP_EOL;
        foreach ($allCallbacks as $callback => $count) {
            $url = $this->generateUrl('gui_status_callback', ['callback' => $callback]);
            $output .= "<a href='$url'>$callback()</a> ($count)" . PHP_EOL;
        }

        $output .= PHP_EOL . PHP_EOL . $out;

        return new Response($output);
    }

    /**
     * Show callback
     *
     * @param string $callback
     * @param string $component
     *
     * @return Response
     * @Route("/callback", name="phlexible_gui_status_callback")
     */
    public function callbackAction($callback, $component = null)
    {
        $components = $this->container->getParameter('kernel.bundles');

        $output = '<pre>';

        $result = null;
        if (!$component) {
            $output .= 'Callback: '.$callback.'()'.PHP_EOL.PHP_EOL;
            $result = [];
            foreach ($components as $class) {
                if (method_exists($class, $callback)) {
                    $bundle = new $class();
                    $result = array_merge($result, $bundle->$callback());
                }
            }
        } else {
            $class = $components[$component];
            if (method_exists($class, $callback)) {
                $bundle = new $class();
                $result = $bundle->$callback();
                $output .= 'Callback:  '.$callback.'()'.PHP_EOL;
                $output .= 'Component: '.$component.PHP_EOL.PHP_EOL;
            }
        }

        if ($result) {
            $output .= print_r($result, true);
        }

        return new Response($output);
    }

    /**
     * Show events
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
        $output .= str_repeat('=', 3) . str_pad(' Events / Listeners ', 80, '=') . PHP_EOL . PHP_EOL;

        foreach ($listenerNames as $listenerName) {
            $listeners = $dispatcher->getListeners($listenerName);

            $output .= $listenerName . ' (<a href="#' . $listenerName . '">' . count($listeners) . ' listeners</a>)' . PHP_EOL;
        }

        foreach ($listenerNames as $listenerName) {
            $listeners = $dispatcher->getListeners($listenerName);
            //sort($observers);

            if (!$listenerName) {
                $listenerName = '(global)';
            }

            $output .= PHP_EOL . PHP_EOL;
            $output .= str_repeat('-', 3);
            $output .= '<a name="' . $listenerName . '"></a>' . str_pad(' ' . $listenerName . ' ', 80, '-');
            $output .= PHP_EOL . PHP_EOL;

            foreach ($listeners as $listener) {
                if (is_array($listener)) {
                    if (is_object($listener[0])) {
                        $listener = get_class($listener[0]) . '->' . $listener[1] . '()';
                    } else {
                        $listener = implode('::', $listener) . '()';
                    }
                }
                $output .= '* ' . $listener . PHP_EOL;
            }
        }

        return new Response($output);
    }

    /**
     * Show routes
     *
     * @return Response
     * @Route("/routes", name="phlexible_gui_status_routes")
     */
    public function routesAction()
    {
        $router = $this->get('router');
        $nameParser = $this->get('controller_name_converter');

        $routes = $router->getRouteCollection();
        $paths = [];
        foreach ($routes as $name => $route) {
            /* @var $route RoutingRoute  */

            /*
            $data = array();
            $vars = $route->getVariables();
            foreach ($vars as $var) {
                $data[$var] = '{' . $var . '}';
            }
            */

            if ($route->hasDefault('_controller')) {
                try {
                    $route->setDefault('_controller', $nameParser->build($route->getDefault('_controller')));
                } catch (\InvalidArgumentException $e) {
                }
            }

            $paths[$name] = $route;
        }

        ksort($paths);

        $output = '<pre>' .
            str_pad('Name', 60) . ' ' .
            str_pad('Method', 10) . ' ' .
            str_pad('Scheme', 10) . ' ' .
            str_pad('Host', 10) . ' ' .
            'Path' . ' ' .
            PHP_EOL;

        foreach ($paths as $name => $route) {
            $output .=
                str_pad($name, 60) . ' ' .
                str_pad($route->getMethods() ? implode(',', $route->getMethods()) : 'ANY', 10) . ' ' .
                str_pad($route->getSchemes() ? implode(',', $route->getSchemes()) : 'ANY', 10) . ' ' .
                str_pad($route->getHost() ? $route->getHost() : 'ANY', 10) . ' ' .
                $route->getPath() . ' ' .
                PHP_EOL;
        }

        return new Response($output);
    }

    /**
     * phpinfo
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
     * Show versions
     *
     * @return Response
     * @Route("/versions", name="phlexible_gui_status_versions")
     */
    public function versionsAction()
    {
        $output = '';
        $output .= '<div>PHP: ' . PHP_VERSION . '</div>';
        $output .= '<div>phlexible: 1.0.0</div>';
        $output .= '<div>Symfony: ' . \Symfony\Component\HttpKernel\Kernel::VERSION . '</div>';
        $output .= '<div>Doctrine DBAL: ' . \Doctrine\DBAL\Version::VERSION . '</div>';
        $output .= '<div>Doctrine ORM: ' . \Doctrine\ORM\Version::VERSION . '</div>';


        return new Response($output);
    }

    /**
     * Show load
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
     * Show security context
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
        $output .= 'Token class: ' . get_class($token) . PHP_EOL;
        $output .= 'User class:  ' . (is_object($user) ? get_class($user) : $user) . PHP_EOL;
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
     * Show session
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
                $o = 'array ' . count($value);
            } else {
                $o = $value;
                if (@unserialize($o)) {
                    $o = unserialize($o);
                }
            }
            $output .= '<li>'.$key . ': ' . $o . '</li>';
        }
        $output .= '</ul>';

        return new Response($output);
    }
}

