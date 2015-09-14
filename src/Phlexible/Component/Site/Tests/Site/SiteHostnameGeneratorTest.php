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
