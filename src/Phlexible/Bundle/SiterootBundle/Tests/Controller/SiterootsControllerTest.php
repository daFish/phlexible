<?php

namespace Phlexible\Bundle\SiterootBundle\Tests\Controller;

use Phlexible\Bundle\SiterootBundle\Entity\Navigation;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SiterootsControllerTest
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootsControllerTest extends WebTestCase
{
    /**
     * @group functional
     */
    public function testGetSiterootsReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/siteroots');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('siteroots', $data);
        $this->assertArrayHasKey('count', $data);
    }

    /**
     * @group functional
     */
    public function testGetSiterootReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $siterootManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $siteroot = new Siteroot();
        $siterootManager->updateSiteroot($siteroot);
        $siterootId = $siteroot->getId();

        $client->request('GET', "/admin/rest/siteroots/$siterootId");
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('siteroot', $data);
        $this->assertArrayHasKey('id', $data['siteroot']);
        $this->assertSame($siterootId, $data['siteroot']['id']);
    }

    /**
     * @group functional
     */
    public function testGetSiterootRespondsWith404ForUnknownSiteroot()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/siteroots/invalid');
        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * @group functional
     */
    public function testPostSiterootsCreatesNewSiteroot()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $siterootManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $data = array(
            'siteroot' => array(
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

        $client->request('POST', "/admin/rest/siteroots", array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());
        $this->assertCount(1, $siterootManager->findAll());
        $siteroot = current($siterootManager->findAll());
        $this->assertTrue($siteroot->isDefault());
        $specialTid = $siteroot->getSpecialTid('de', 'testSpecialTid');
        $this->assertSame(123, $specialTid);
        $navigation = $siteroot->getNavigations()->first();
        $this->assertSame('testNavigation', $navigation->getTitle());
        $url = $siteroot->getUrls()->first();
        $this->assertSame('testHostname', $url->getHostname());
    }

    /**
     * @group functional
     */
    public function testPostSiterootsRespondsWith400OnError()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $siterootManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $data = array(
            'siteroot' => array(
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

        $client->request('POST', "/admin/rest/siteroots", array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());
        $this->assertCount(0, $siterootManager->findAll());
    }

    /**
     * @group functional
     */
    public function testPutSiterootUpdatesExistingSiteroot()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $siterootManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $siteroot = new Siteroot();
        $siteroot->setDefault(false);
        $navigation = new Navigation();
        $navigation->setTitle('inititalTitle');
        $siteroot->addNavigation($navigation);
        $url = new Url();
        $url->setHostname('inititalHostname');
        $siteroot->addUrl($url);
        $siterootManager->updateSiteroot($siteroot);
        $siterootId = $siteroot->getId();

        $data = array(
            'siteroot' => array(
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

        $client->request('PUT', "/admin/rest/siteroots/$siterootId", array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());
        $this->assertCount(1, $siterootManager->findAll());
        $siteroot = current($siterootManager->findAll());
        $this->assertTrue($siteroot->isDefault());
        $navigation = $siteroot->getNavigations()->first();
        $this->assertSame('testTitle', $navigation->getTitle());
        $url = $siteroot->getUrls()->first();
        $this->assertSame('testHostname', $url->getHostname());
    }

    /**
     * @group functional
     */
    public function testPutSiterootRespondsWith404ForUnknownSiteroot()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $siterootManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $data = array(
            'siteroot' => array(
                'default' => true,
                'navigations' => array(
                    array('title' => 'testNavigation'),
                ),
                'urls' => array(
                    array('hostname' => 'testHostname'),
                ),
            ),
        );

        $client->request('PUT', "/admin/rest/siteroots/invalid", array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
        $this->assertCount(0, $siterootManager->findAll());
    }

    /**
     * @group functional
     */
    public function testDeleteSiterootDeletesSiteroot()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $siterootManager = static::$kernel->getContainer()->get('phlexible_siteroot.siteroot_manager');

        $siteroot = new Siteroot();
        $siterootManager->updateSiteroot($siteroot);
        $siterootId = $siteroot->getId();

        $client->request('DELETE', "/admin/rest/siteroots/$siterootId");
        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());
    }

    /**
     * @group functional
     */
    public function testDeleteSiterootRespondsWith404ForUnknownSiteroot()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('DELETE', "/admin/rest/siteroots/invalid");
        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
    }
}