<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Config;

use Phlexible\Bundle\GuiBundle\Config\Config;

/**
 * Config test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyValues()
    {
        $config = new Config();

        $this->assertCount(0, $config->all());
        $this->assertFalse($config->has('foo'));
    }

    /**
     * @expectedException \Phlexible\Bundle\GuiBundle\Exception\InvalidArgumentException
     */
    public function testGetThrowsExceptionOnInvalidKey()
    {
        $config = new Config();

        $config->get('foo');
    }

    /**
     * @expectedException \Phlexible\Bundle\GuiBundle\Exception\InvalidArgumentException
     */
    public function testSetThrowsExceptionOnInvalidValue()
    {
        $config = new Config();

        $config->set('foo', new \stdClass());
    }

    public function testGetSetHas()
    {
        $config = new Config();

        $config->set('foo', 'bar');
        $this->assertTrue($config->has('foo'));
        $this->assertSame('bar', $config->get('foo'));
        $this->assertSame(array('foo' => 'bar'), $config->all());
    }
}
