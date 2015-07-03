<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Controller;

use Phlexible\Bundle\GuiBundle\Controller\FrameController;
use Phlexible\Bundle\GuiBundle\Routing\ExtractedRoutes;
use Symfony\Component\HttpFoundation\Request;

/**
 * Frame controller test
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

        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->get('phlexible_gui.route_extractor')->willReturn($routeExtractor->reveal());

        $controller = new FrameController();
        $controller->setContainer($container->reveal());

        $response = $controller->routesAction($request);

        $this->assertSame('Phlexible.Router.setData({"baseUrl":"","basePath":"","routes":["test"]});', $response->getContent());
    }
}
