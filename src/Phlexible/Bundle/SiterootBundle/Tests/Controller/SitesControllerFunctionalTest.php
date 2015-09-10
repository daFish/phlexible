<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\Tests\Controller;

use Phlexible\Component\Site\Domain\Site;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SitesControllerFunctionalTest extends WebTestCase
{
    public function testGetSitesReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/sites');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('sites', $data);
        $this->assertArrayHasKey('total', $data);
    }

    public function testGetSiteReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $siteManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $site = new Site();
        $siteManager->updateSite($site);
        $siteId = $site->getId();

        $client->request('GET', "/admin/rest/sites/$siteId");
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('site', $data);
        $this->assertArrayHasKey('id', $data['site']);
        $this->assertSame($siteId, $data['site']['id']);
    }

    /**
     * @group functional
     */
    public function testGetSiteRespondsWith404ForUnknownSite()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/sites/invalid');
        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * @group functional
     */
    public function testPostSitesCreatesNewSite()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $siteManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $data = array(
            'site' => array(
                'default' => true,
                'titles' => array('de' => 'testDe', 'en' => 'testEn'),
                'specialTids' => array(
                    array('name' => 'testSpecialTid', 'language' => 'de', 'treeId' => 123)
                ),
                'navigations' => array(
                    array('title' => 'testNavigation', 'startTreeId' => 123),
                ),
                'urls' => array(
                    array('hostname' => 'testHostname', 'language' => 'de', 'target' => 123),
                ),
            ),
        );

        $client->request('POST', "/admin/rest/sites", array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());
        $this->assertCount(1, $siteManager->findAll());
        $site = current($siteManager->findAll());
        $this->assertTrue($site->isDefault());
        $specialTid = $site->getSpecialTid('de', 'testSpecialTid');
        $this->assertSame(123, $specialTid);
        $navigation = $site->getNavigations()->first();
        $this->assertSame('testNavigation', $navigation->getTitle());
        $url = $site->getUrls()->first();
        $this->assertSame('testHostname', $url->getHostname());
    }

    /**
     * @group functional
     */
    public function testPostSitesRespondsWith400OnError()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $siteManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $data = array(
            'site' => array(
                'default' => true,
                //'titles' => array('de' => 'testDe', 'en' => 'testEn'),
                'navigations' => array(
                    array('title' => null, 'handler' => 'testHandler'),
                ),
                'urls' => array(
                    array('hostname' => null, 'language' => 'de', 'target' => 123),
                ),
            ),
        );

        $client->request('POST', "/admin/rest/sites", array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());
        $this->assertCount(0, $siteManager->findAll());
    }

    /**
     * @group functional
     */
    public function testPutSiteUpdatesExistingSite()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $siteManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $site = new Site();
        $site->setHostname('www.test.com');
        $siteManager->updateSite($site);
        $siteId = $site->getId();

        $data = array(
            'site' => array(
                'default' => true,
                //'titles' => array('de' => 'testDe', 'en' => 'testEn'),
                'navigations' => array(
                    array('title' => 'testTitle', 'startTreeId' => 123),
                ),
                'urls' => array(
                    array('hostname' => 'testHostname', 'language' => 'de', 'target' => 123),
                ),
            ),
        );

        $client->request('PUT', "/admin/rest/sites/$siteId", array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());
        $this->assertCount(1, $siteManager->findAll());
        $site = current($siteManager->findAll());
        $this->assertTrue($site->isDefault());
        $navigation = $site->getNavigations()->first();
        $this->assertSame('testTitle', $navigation->getTitle());
        $url = $site->getUrls()->first();
        $this->assertSame('testHostname', $url->getHostname());
    }

    /**
     * @group functional
     */
    public function testPutSiteRespondsWith404ForUnknownSite()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $siteManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $data = array(
            'site' => array(
                'default' => true,
                'navigations' => array(
                    array('title' => 'testNavigation'),
                ),
                'urls' => array(
                    array('hostname' => 'testHostname'),
                ),
            ),
        );

        $client->request('PUT', "/admin/rest/sites/invalid", array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertCount(0, $siteManager->findAll());
    }

    /**
     * @group functional
     */
    public function testDeleteSiteDeletesSite()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $siteManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $site = new Site();
        $siteManager->updateSite($site);
        $siteId = $site->getId();

        $client->request('DELETE', "/admin/rest/sites/$siteId");
        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());
    }

    /**
     * @group functional
     */
    public function testDeleteSiteRespondsWith404ForUnknownSite()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('DELETE', "/admin/rest/sites/invalid");
        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
    }
}
