<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\Tests\Site;

use Phlexible\Component\Site\Site\SiteHostnameMapper;

/**
 * Site hostname mapper test
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
