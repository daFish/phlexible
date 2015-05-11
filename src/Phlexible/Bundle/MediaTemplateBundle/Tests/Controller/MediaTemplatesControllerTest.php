<?php

namespace Phlexible\Bundle\MediaTemplateBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MediaTemplatesControllerTest
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaTemplatesControllerTest extends WebTestCase
{
    /**
     * @group functional
     */
    public function testGetMediaTemplatesReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/mediatemplates');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('mediatemplates', $data);
        $this->assertArrayHasKey('count', $data);
    }

    /**
     * @group functional
     */
    public function testGetMediaTemplateReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/mediatemplates/cm_image--fullsize');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('mediatemplate', $data);
        $this->assertArrayHasKey('key', $data['mediatemplate']);
        $this->assertSame('cm_image--fullsize', $data['mediatemplate']['key']);
    }

    /**
     * @group functional
     */
    public function testGetMediaTemplateRespondsWith404ForUnknownMessage()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/mediatemplates/invalid');
        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
    }
}