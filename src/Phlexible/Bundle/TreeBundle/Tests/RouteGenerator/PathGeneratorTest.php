<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tests\RouteGenerator;

use Phlexible\Bundle\TreeBundle\RouteGenerator\PathGenerator;
use Prophecy\Argument;

/**
 * Path generator test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PathGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGeneratePath()
    {
        $decorator = $this->prophesize('Phlexible\Bundle\TreeBundle\RouteGenerator\PathDecoratorInterface');
        $tree = $this->prophesize('Phlexible\Bundle\TreeBundle\Tree\Tree');
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getTree()->willReturn($tree->reveal());
        $tree->getPath($node)->willReturn(array($node));
        $tree->isViewable($node, 'de')->willReturn(true);
        $tree->getField($node, 'navigation', 'de')->willReturn('foo');
        $decorator->decoratePath(Argument::type('Phlexible\Bundle\TreeBundle\RouteGenerator\Path'), $node, 'de')->shouldBeCalled();

        $generator = new PathGenerator(array($decorator->reveal()));

        $this->assertSame('/foo', $generator->generatePath($node->reveal(), 'de'));
    }
}
