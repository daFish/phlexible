<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaTypeBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MediaTypesControllerTest
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaTypesControllerTest extends WebTestCase
{
    /**
     * @group functional
     */
    public function testGetMediaTypesReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/mediatypes');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('mediatypes', $data);
        $this->assertArrayHasKey('count', $data);
    }
}
