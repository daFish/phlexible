<?php

namespace Phlexible\Bundle\QueueBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class JobsControllerTest
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class JobsControllerTest extends WebTestCase
{
    /**
     * @group functional
     */
    public function testGetJobsReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/jobs');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('jobs', $data);
        $this->assertArrayHasKey('count', $data);
    }

    /**
     * @group functional
     */
    public function testGetJobReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/jobs/dffae674-ecb2-11e4-b400-001e677a6817');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('job', $data);
        $this->assertArrayHasKey('id', $data['job']);
        $this->assertSame('dffae674-ecb2-11e4-b400-001e677a6817', $data['job']['id']);
    }

    /**
     * @group functional
     */
    public function testGetJobRespondsWith404ForUnknownJob()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/jobs/invalid');
        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
    }
}