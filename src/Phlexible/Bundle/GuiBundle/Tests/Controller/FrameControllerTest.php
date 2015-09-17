<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Controller;

use Phlexible\Bundle\GuiBundle\Controller\FrameController;
use Phlexible\Bundle\GuiBundle\Routing\ExtractedRoutes;
use Symfony\Component\HttpFoundation\Request;

/**
 * Frame controller test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FrameControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testRoutesAction()
    {
        $request = new Request();

        $extractedRoutes = new ExtractedRoutes('', '', array('test'));

        $routeExtractor = $this->prophesize('Phlexible\Bundle\GuiBundle\Routing\RouteExtractorInterface');
        $routeExtractor->extract($request)->willReturn($extractedRoutes);

        $fileResource = $this->prophesize('Puli\Repository\Resource\FileResource');
        $fileResource->getBody()->willReturn('router;');

        $puliRepository = $this->prophesize('Puli\Repository\Api\ResourceRepository');
        $puliRepository->get('/phlexible/phlexiblegui/scripts/util/Router.js')->willReturn($fileResource);

        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->get('phlexible_gui.route_extractor')->willReturn($routeExtractor->reveal());
        $container->get('puli.repository')->willReturn($puliRepository->reveal());

        $controller = new FrameController();
        $controller->setContainer($container->reveal());

        $response = $controller->routesAction($request);

        $this->assertSame('router;Phlexible.Router = Ext.create("Phlexible.gui.util.Router", {"baseUrl":"","basePath":"","routes":["test"]});', $response->getContent());
    }
}
