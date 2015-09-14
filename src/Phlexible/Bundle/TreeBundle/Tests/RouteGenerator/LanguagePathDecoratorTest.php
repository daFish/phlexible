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

use Phlexible\Bundle\TreeBundle\RouteGenerator\LanguagePathDecorator;
use Phlexible\Bundle\TreeBundle\RouteGenerator\Path;

/**
 * Language path decorator test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LanguagePathDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testDecorate()
    {
        $path = new Path();
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');

        $decorator = new LanguagePathDecorator();
        $decorator->decoratePath($path, $node->reveal(), 'en');

        $this->assertSame('/en', (string) $path);
    }

    public function testDecoratePrependsValueToPath()
    {
        $path = new Path(array('/foo.html'));
        $node = $this->prophesize('Phlexible\Bundle\TreeBundle\Node\NodeContext');

        $decorator = new LanguagePathDecorator();
        $decorator->decoratePath($path, $node->reveal(), 'de');

        $this->assertSame('/de/foo.html', (string) $path);
    }
}
