<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MessagesControllerTest
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessagesControllerTest extends WebTestCase
{
    /**
     * @group functional
     */
    public function testGetMessagesReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/messages');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('messages', $data);
        $this->assertArrayHasKey('count', $data);
    }

    /**
     * @group functional
     */
    public function testGetMessageReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/messages/8ee6de30-4c39-11e5-8693-8418ea4ec907');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('id', $data['message']);
        $this->assertSame('8ee6de30-4c39-11e5-8693-8418ea4ec907', $data['message']['id']);
    }

    /**
     * @group functional
     */
    public function testGetMessageRespondsWith404ForUnknownMessage()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/messages/invalid');
        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
    }
}
