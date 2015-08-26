<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MetaSetBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MetaSetsControllerTest
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetsControllerTest extends WebTestCase
{
    /**
     * @group functional
     */
    public function testGetMetaSetsReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/metasets');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('metasets', $data);
        $this->assertArrayHasKey('count', $data);
    }

    /**
     * @group functional
     */
    public function testGetMetaSetReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/metasets/3330275c-7a2d-11e4-b400-001e677a6817');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('metaset', $data);
        $this->assertArrayHasKey('id', $data['metaset']);
        $this->assertSame('3330275c-7a2d-11e4-b400-001e677a6817', $data['metaset']['id']);
    }

    /**
     * @group functional
     */
    public function testGetMetaSetRespondsWith404ForUnknownMetaSet()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/metasets/invalid');
        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
    }
}
