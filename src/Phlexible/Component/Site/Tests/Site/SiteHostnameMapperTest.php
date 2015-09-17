<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Site\Tests\Site;

use Phlexible\Component\Site\Site\SiteHostnameMapper;

/**
 * Site hostname mapper test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiteHostnameMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testToLocal()
    {
        $mapper = new SiteHostnameMapper(array('www.test.com' => 'test.dev'));

        $this->assertSame('test.dev', $mapper->toLocal('www.test.com'));
    }

    public function testFromLocal()
    {
        $mapper = new SiteHostnameMapper(array('www.test.com' => 'test.dev'));

        $this->assertSame('www.test.com', $mapper->fromLocal('test.dev'));
    }
}
