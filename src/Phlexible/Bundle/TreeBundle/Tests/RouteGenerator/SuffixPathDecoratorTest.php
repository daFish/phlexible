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

use Phlexible\Bundle\TreeBundle\RouteGenerator\SuffixPathDecorator;
use Phlexible\Bundle\TreeBundle\RouteGenerator\Path;

/**
 * Suffix path decorator test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SuffixPathDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testDecorate()
    {
        $path = new Path();
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');

        $decorator = new SuffixPathDecorator();
        $decorator->decoratePath($path, $node->reveal(), 'en');

        $this->assertSame('.html', (string) $path);
    }

    public function testDecorateAppendsValueToPath()
    {
        $path = new Path(array('/de/foo'));
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');

        $decorator = new SuffixPathDecorator();
        $decorator->decoratePath($path, $node->reveal(), 'de');

        $this->assertSame('/de/foo.html', (string) $path);
    }

    public function testDecorateWithCustomSuffix()
    {
        $path = new Path(array('/en/bar'));
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');

        $decorator = new SuffixPathDecorator('.php');
        $decorator->decoratePath($path, $node->reveal(), 'en');

        $this->assertSame('/en/bar.php', (string) $path);
    }
}
