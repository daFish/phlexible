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
 * Class SubscriptionsControllerTest.
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
