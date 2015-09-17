<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Util\Tests;

use Phlexible\Component\Util\ArrayUtil;

/**
 * Array util Test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayUtil
     */
    private $util;

    public function setUp()
    {
        $this->util = new ArrayUtil();
    }

    public function testColumnEmpty()
    {
        $src = array();

        $result = $this->util->column($src, 'a');

        $expected = array();

        $this->assertEquals($expected, $result);
    }

    public function testColumnNormal()
    {
        $src = array(
            array('a1', 'b1'),
            array('a2', 'b2'),
            array('a3', 'b3'),
        );

        $result = $this->util->column($src, 0);

        $expected = array('a1', 'a2', 'a3');

        $this->assertEquals($expected, $result);
    }

    public function testColumnAssoziative()
    {
        $src = array(
            array('d' => 'a1', 'e' => 'b1'),
            array('d' => 'a2', 'e' => 'b2'),
            array('d' => 'a3', 'e' => 'b3'),
        );

        $result = $this->util->column($src, 'd');

        $expected = array('a1', 'a2', 'a3');

        $this->assertEquals($expected, $result);
    }

    public function testColumnAssoziativeMissingKey()
    {
        $src = array(
            array('d' => 'a1', 'e' => 'b1'),
            array('x' => 'a2', 'e' => 'b2'),
            array('d' => 'a3', 'e' => 'b3'),
        );

        $result = $this->util->column($src, 'd');

        $expected = array('a1', null, 'a3');

        $this->assertEquals($expected, $result);
    }

    public function testColumnAssoziativeMissingKeyWithSkip()
    {
        $src = array(
            array('d' => 'a1', 'e' => 'b1'),
            array('x' => 'a2', 'e' => 'b2'),
            array('d' => 'a3', 'e' => 'b3'),
        );

        $result = $this->util->column($src, 'd', true);

        $expected = array(0 => 'a1', 2 => 'a3');

        $this->assertEquals($expected, $result);
    }

    public function testColumnAssoziativeMissingKeyWithSkipEmpty()
    {
        $src = array(
            array('d' => '', 'e' => 'b1'),
            array('x' => 'a2', 'e' => 'b2'),
            array('d' => 'a3', 'e' => 'b3'),
        );

        $result = $this->util->column($src, 'd', true, true);

        $expected = array(2 => 'a3');

        $this->assertEquals($expected, $result);
    }

    public function testGet()
    {
        $src = array('eins', 'zwei', 5 => 'fünf', 'abc' => 'def');

        $this->assertEquals('eins', $this->util->get($src, 0));
        $this->assertEquals('zwei', $this->util->get($src, 1));
        $this->assertEquals('fünf', $this->util->get($src, 5));
        $this->assertEquals('fünf', $this->util->get($src, 5, 'default'));
        $this->assertEquals('def', $this->util->get($src, 'abc'));
        $this->assertEquals('def', $this->util->get($src, 'abc', 'default'));
        $this->assertEquals('default', $this->util->get($src, 'xxx', 'default'));

        $this->assertNull($this->util->get($src, 'xxx'));
    }

    public function testGroupByEmpty()
    {
        $src = array();

        $result = $this->util->groupBy($src, 'col');

        $expected = array();

        $this->assertEquals($expected, $result);
    }

    public function testGroupByOneColumn()
    {
        $src = array(
            array('key' => 1, 'value' => '1'),
            array('key' => 3, 'value' => '1'),
            array('key' => 2, 'value' => '1'),
            array('key' => 3, 'value' => '2'),
            array('key' => 3, 'value' => '3'),
            array('key' => 2, 'value' => '2'),
        );

        $result = $this->util->groupBy($src, 'key');

        $expected = array(
            '1' => array(
                array('key' => 1, 'value' => '1'),
            ),
            '3' => array(
                array('key' => 3, 'value' => '1'),
                array('key' => 3, 'value' => '2'),
                array('key' => 3, 'value' => '3'),
            ),
            '2' => array(
                array('key' => 2, 'value' => '1'),
                array('key' => 2, 'value' => '2'),
            ),
        );

        $this->assertEquals($expected, $result);
    }

    public function testGroupByTwoColumns()
    {
        $src = array(
            array('keyA' => 1, 'keyB' => 1, 'value' => '1'),
            array('keyA' => 3, 'keyB' => 1, 'value' => '2'),
            array('keyA' => 2, 'keyB' => 1, 'value' => '1'),
            array('keyA' => 1, 'keyB' => 1, 'value' => '2'),
            array('keyA' => 2, 'keyB' => 1, 'value' => '2'),
            array('keyA' => 1, 'keyB' => 2, 'value' => '1'),
            array('keyA' => 2, 'keyB' => 2, 'value' => '1'),
            array('keyA' => 2, 'keyB' => 2, 'value' => '2'),
            array('keyA' => 3, 'keyB' => 1, 'value' => '1'),
            array('keyA' => 2, 'keyB' => 3, 'value' => '1'),
            array('keyA' => 3, 'keyB' => 1, 'value' => '3'),
        );

        $result = $this->util->groupBy($src, array('keyA', 'keyB'));

        $expected = array(
            '1' => array(
                '1' => array(
                    array('keyA' => 1, 'keyB' => 1, 'value' => '1'),
                    array('keyA' => 1, 'keyB' => 1, 'value' => '2'),
                ),
                '2' => array(
                    array('keyA' => 1, 'keyB' => 2, 'value' => '1'),
                ),
            ),
            '3' => array(
                '1' => array(
                    array('keyA' => 3, 'keyB' => 1, 'value' => '2'),
                    array('keyA' => 3, 'keyB' => 1, 'value' => '1'),
                    array('keyA' => 3, 'keyB' => 1, 'value' => '3'),
                ),
            ),
            '2' => array(
                '1' => array(
                    array('keyA' => 2, 'keyB' => 1, 'value' => '1'),
                    array('keyA' => 2, 'keyB' => 1, 'value' => '2'),
                ),
                '2' => array(
                    array('keyA' => 2, 'keyB' => 2, 'value' => '1'),
                    array('keyA' => 2, 'keyB' => 2, 'value' => '2'),
                ),
                '3' => array(
                    array('keyA' => 2, 'keyB' => 3, 'value' => '1'),
                ),
            ),
        );

        $this->assertEquals($expected, $result);
    }
}
