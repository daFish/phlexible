<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
