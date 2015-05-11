<?php

namespace Phlexible\Bundle\MessageBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SubscriptionsControllerTest
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SubscriptionsControllerTest extends WebTestCase
{
    /**
     * @group functional
     */
    public function testGetSubscriptionsReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/subscriptions');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('subscriptions', $data);
        $this->assertArrayHasKey('count', $data);
    }
}