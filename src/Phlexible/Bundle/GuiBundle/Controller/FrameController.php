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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Frame controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FrameController extends Controller
{
    /**
     * Render Frame
     *
     * @param Request $request
     *
     * @return array
     * @Route("", name="phlexible_gui")
     * @Method("GET")
     * @Template
     */
    public function indexAction(Request $request)
    {
        $viewIndex = $this->get('phlexible_gui.view.index');

        return [
            'scripts'  => $viewIndex->get($request),
            'noScript' => $viewIndex->getNoScript(),
        ];
    }

    /**
     * Return file
     *
     * @param string $path
     *
     * @return Response
     * @Route("/gui/load/{path}.js", name="phlexible_gui_load", requirements={"path": ".*"})
     * @Method("GET")
     */
    public function loadAction($path)
    {
        $x = explode('/', $path);
        array_shift($x);
        $a = array_shift($x);
        $b = implode('/', $x);
        $y = "/phlexible/phlexible$a/scripts/$b.js";
        $puli = $this->get('puli.repository');
        $z = $puli->get($y);

        return new Response($z->getBody(), 200, array('Content-type' => 'text/javascript'));
    }

    /**
     * Return configuration
     *
     * @return JsonResponse
     * @Route("/gui/config", name="phlexible_gui_config")
     * @Method("GET")
     */
    public function configAction()
    {
        $configBuilder = $this->get('phlexible_gui.config_builder');
        $config = $configBuilder->build();

        return new JsonResponse($config->all());
    }

    /**
     * Return menu
     *
     * @return JsonResponse
     * @Route("/gui/menu", name="phlexible_gui_menu")
     * @Method("GET")
     */
    public function menuAction()
    {
        $loader = $this->get('phlexible_gui.menu.loader');
        $items = $loader->load();
        $data = $items->toArray();

        return new JsonResponse($data);
    }

    /**
     * Return routes
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/gui/routes", name="phlexible_gui_routes")
     * @Method("GET")
     */
    public function routesAction(Request $request)
    {
        $routeExtractor = $this->get('phlexible_gui.route_extractor');
        $extractedRoutes = $routeExtractor->extract($request);

        $content = '';
        $content .= file_get_contents(dirname(__DIR__).'/Resources/scripts/util/Router.js');
        $content .= sprintf('Phlexible.Router = Ext.create("Phlexible.gui.util.Router", %s);', json_encode(array(
            'baseUrl' => $extractedRoutes->getBaseUrl(),
            'basePath' => $extractedRoutes->getBasePath(),
            'routes' => $extractedRoutes->getRoutes(),
        )));

        return new Response($content, 200, ['Content-type' => 'text/javascript; charset=utf-8']);
    }
}
