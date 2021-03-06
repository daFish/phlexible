<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Tests\RouteGenerator;

use Phlexible\Bundle\TreeBundle\RouteGenerator\PathGenerator;
use Prophecy\Argument;

/**
 * Path generator test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PathGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGeneratePath()
    {
        $decorator = $this->prophesize('Phlexible\Bundle\TreeBundle\RouteGenerator\PathDecoratorInterface');
        $tree = $this->prophesize('Phlexible\Component\Tree\Tree');
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');
        $node->getTree()->willReturn($tree->reveal());
        $node->isViewable('de')->willReturn(true);
        $node->getField('navigation', 'de')->willReturn('foo');
        $tree->getPath($node)->willReturn(array($node));
        $decorator->decoratePath(Argument::type('Phlexible\Bundle\TreeBundle\RouteGenerator\Path'), $node, 'de')->shouldBeCalled();

        $generator = new PathGenerator(array($decorator->reveal()));

        $this->assertSame('/foo', $generator->generatePath($node->reveal(), 'de'));
    }
}
