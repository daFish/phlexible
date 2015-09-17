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

use Phlexible\Bundle\TreeBundle\RouteGenerator\Path;

/**
 * Path test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PathTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $path = new Path();

        $this->assertSame('', (string) $path);
    }

    public function testConstructorSetsValues()
    {
        $path = new Path(array('/foo', '.html'));

        $this->assertSame('/foo.html', (string) $path);
    }

    public function testAppend()
    {
        $path = new Path(array('/foo'));

        $path->append('.html');

        $this->assertSame('/foo.html', (string) $path);
    }

    public function testPrepend()
    {
        $path = new Path(array('/foo'));

        $path->prepend('/en');

        $this->assertSame('/en/foo', (string) $path);
    }

    public function testCount()
    {
        $path = new Path(array('a', 'b', 'c'));

        $this->assertCount(3, $path);
    }
}
