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
    /**
     * @group functional
     */
    public function testGetSitesReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz', 'HTTP_ACCEPT' => 'application/json'));

        $client->request('GET', '/admin/rest/sites');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('sites', $data);
        $this->assertArrayHasKey('total', $data);
    }

    /**
     * @group functional
     */
    public function testGetSiteReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz', 'HTTP_ACCEPT' => 'application/json'));

        $siteManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $site = new Site();
        $siteManager->updateSite($site);
        $siteId = $site->getId();

        $client->request('GET', "/admin/rest/sites/$siteId");
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('default', $data);
        $this->assertArrayHasKey('createdAt', $data);
        $this->assertArrayHasKey('modifiedAt', $data);
        $this->assertArrayHasKey('titles', $data);
        $this->assertArrayHasKey('properties', $data);
        $this->assertArrayHasKey('nodeAliases', $data);
        $this->assertArrayHasKey('navigations', $data);
        $this->assertArrayHasKey('entryPoints', $data);
        $this->assertArrayHasKey('nodeConstraints', $data);
        $this->assertSame($siteId, $data['id']);
    }

    /**
     * @group functional
     */
    public function testGetSiteRespondsWith404ForUnknownSite()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz', 'HTTP_ACCEPT' => 'application/json'));

        $client->request('GET', '/admin/rest/sites/invalid');
        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * @group functional
     */
    public function testPostSitesCreatesNewSite()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz', 'HTTP_ACCEPT' => 'application/json'));

        $siteManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $data = array(
            'site' => array(
                'hostname' => 'www.test.com',
                'default' => true,
                'titles' => array('de' => 'testDe', 'en' => 'testEn'),
                'createdAt' => date('Y-m-d H:i:s'),
                'createdBy' => 'test',
                'modifiedAt' => date('Y-m-d H:i:s'),
                'modifiedBy' => 'test',
                'nodeAliases' => array(
                    array('name' => 'testSpecialTid', 'language' => 'de', 'nodeId' => 123),
                ),
                #'navigations' => array(
                #    array('name' => 'testNavigation', 'nodeId' => 123),
                #),
                #'entryPoints' => array(
                #    array('hostname' => 'testHostname', 'language' => 'de', 'nodeId' => 123),
                #),
            ),
        );

        $client->request('POST', '/admin/rest/sites', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());
        $this->assertCount(1, $siteManager->findAll());
        $site = current($siteManager->findAll());
        $this->assertTrue($site->isDefault());
        $this->assertSame('www.test.com', $site->getHostname());

        return;
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
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz', 'HTTP_ACCEPT' => 'application/json'));

        $siteManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $data = array(
            'site' => array(
                'default' => true,
                'titles' => array('de' => 'testDe', 'en' => 'testEn'),
                'createdAt' => date('Y-m-d H:i:s'),
                'createdBy' => 'test',
                'modifiedAt' => date('Y-m-d H:i:s'),
                'modifiedBy' => 'test',
            ),
        );

        $client->request('POST', '/admin/rest/sites', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());
        $this->assertCount(0, $siteManager->findAll());
    }

    /**
     * @group functional
     */
    public function testPutSiteUpdatesExistingSite()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz', 'HTTP_ACCEPT' => 'application/json'));

        $siteManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $site = new Site();
        $site->setHostname('www.test.com');
        $siteManager->updateSite($site);
        $siteId = $site->getId();

        $data = array(
            'site' => array(
                'hostname' => 'www.test.de',
                'default' => true,
                'titles' => array('de' => 'testDe', 'en' => 'testEn'),
                'createdAt' => date('Y-m-d H:i:s'),
                'createdBy' => 'test',
                'modifiedAt' => date('Y-m-d H:i:s'),
                'modifiedBy' => 'test',
            ),
        );

        $client->request('PUT', "/admin/rest/sites/$siteId", array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());
        $this->assertCount(1, $siteManager->findAll());
        $site = current($siteManager->findAll());
        $this->assertTrue($site->isDefault());
        $this->assertSame('www.test.de', $site->getHostname());
    }

    /**
     * @group functional
     */
    public function testPutSiteRespondsWith404ForUnknownSite()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz', 'HTTP_ACCEPT' => 'application/json'));

        $siteManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $data = array(
            'site' => array(
                'hostname' => 'www.test.de',
                'default' => true,
                'titles' => array('de' => 'testDe', 'en' => 'testEn'),
                'createdAt' => date('Y-m-d H:i:s'),
                'createdBy' => 'test',
                'modifiedAt' => date('Y-m-d H:i:s'),
                'modifiedBy' => 'test',
            ),
        );

        $client->request('PUT', '/admin/rest/sites/invalid', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertCount(0, $siteManager->findAll());
    }

    /**
     * @group functional
     */
    public function testDeleteSiteDeletesSite()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz', 'HTTP_ACCEPT' => 'application/json'));

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
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz', 'HTTP_ACCEPT' => 'application/json'));

        $client->request('DELETE', '/admin/rest/sites/invalid');
        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
    }
}
