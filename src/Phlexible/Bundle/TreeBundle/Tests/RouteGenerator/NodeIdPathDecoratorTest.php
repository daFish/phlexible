<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tests\RouteGenerator;

use Phlexible\Bundle\TreeBundle\RouteGenerator\NodeIdPathDecorator;
use Phlexible\Bundle\TreeBundle\RouteGenerator\Path;

/**
 * Node ID path decorator test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeIdPathDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testDecorate()
    {
        $path = new Path();
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getId()->willReturn(123);

        $decorator = new NodeIdPathDecorator();
        $decorator->decoratePath($path, $node->reveal(), 'en');

        $this->assertSame('.123', (string) $path);
    }

    public function testDecorateAppendsValueToPath()
    {
        $path = new Path(array('/de/foo'));
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getId()->willReturn(234);

        $decorator = new NodeIdPathDecorator();
        $decorator->decoratePath($path, $node->reveal(), 'de');

        $this->assertSame('/de/foo.234', (string) $path);
    }

    public function testDecorateWithCustomSuffix()
    {
        $path = new Path(array('/en/bar'));
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getId()->willReturn(345);

        $decorator = new NodeIdPathDecorator('-');
        $decorator->decoratePath($path, $node->reveal(), 'en');

        $this->assertSame('/en/bar-345', (string) $path);
    }
}
