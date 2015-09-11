<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\Tests\Site;

use Phlexible\Bundle\SiterootBundle\Tests\Model\InMemorySiteManager;
use Phlexible\Component\Site\Domain\Site;
use Phlexible\Component\Site\Site\SiteHostnameMapper;
use Phlexible\Component\Site\Site\SiteRequestMatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * Site request matcher test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiteRequestMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testMatchRequest()
    {
        $site = new Site();
        $site->setHostname('www.test.com');
        $matcher = new SiteRequestMatcher(
            new InMemorySiteManager(array($site)),
            new SiteHostnameMapper(array('www.test.com' => 'test.dev'))
        );

        $request = new Request(array(), array(), array(), array(), array(), array('SERVER_NAME' => 'www.test.com', 'SERVER_PORT' => 80));

        $result = $matcher->matchRequest($request);

        $this->assertSame($site, $result);
    }

    public function testMatchRequestWithLocalMapping()
    {
        $site = new Site();
        $site->setHostname('www.test.com');
        $matcher = new SiteRequestMatcher(
            new InMemorySiteManager(array($site)),
            new SiteHostnameMapper(array('www.test.com' => 'test.dev'))
        );

        $request = new Request(array(), array(), array(), array(), array(), array('SERVER_NAME' => 'test.dev', 'SERVER_PORT' => 80));

        $result = $matcher->matchRequest($request);

        $this->assertSame($site, $result);
    }

    public function testMatchRequestWithDefaultFallback()
    {
        $site = new Site();
        $site->setHostname('www.test.com');
        $site->setDefault(true);
        $matcher = new SiteRequestMatcher(
            new InMemorySiteManager(array($site)),
            new SiteHostnameMapper(array('www.test.com' => 'test.dev'))
        );

        $request = new Request(array(), array(), array(), array(), array(), array('SERVER_NAME' => 'invalid.dev', 'SERVER_PORT' => 80));

        $result = $matcher->matchRequest($request);

        $this->assertSame($site, $result);
    }

    public function testMatchRequestWithoutDefaultFallback()
    {
        $site = new Site();
        $site->setHostname('www.test.com');
        $site->setDefault(false);
        $matcher = new SiteRequestMatcher(
            new InMemorySiteManager(array($site)),
            new SiteHostnameMapper(array('www.test.com' => 'test.dev'))
        );

        $request = new Request(array(), array(), array(), array(), array(), array('SERVER_NAME' => 'invalid.dev', 'SERVER_PORT' => 80));

        $result = $matcher->matchRequest($request);

        $this->assertNull($result);
    }
}
