<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\Tests\Site;

use Phlexible\Component\Site\Domain\Site;
use Phlexible\Component\Site\Site\SiteHostnameGenerator;
use Phlexible\Component\Site\Site\SiteHostnameMapper;

/**
 * Site hostname generator test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiteHostnameGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateHostname()
    {
        $site = new Site();
        $site->setHostname('www.test.com');

        $generator = new SiteHostnameGenerator(new SiteHostnameMapper(array()));
        $result = $generator->generate($site);

        $this->assertSame('www.test.com', $result);
    }

    public function testGenerateHostnameWithHostnameMapping()
    {
        $site = new Site();
        $site->setHostname('www.test.com');

        $generator = new SiteHostnameGenerator(new SiteHostnameMapper(array('www.test.com' => 'test.dev')));
        $result = $generator->generate($site);

        $this->assertSame('test.dev', $result);
    }
}
