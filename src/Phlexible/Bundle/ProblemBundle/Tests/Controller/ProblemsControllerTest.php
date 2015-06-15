<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ProblemBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ProblemsControllerTest
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ProblemsControllerTest extends WebTestCase
{
    /**
     * @group functional
     */
    public function testGetProblemsReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/problems');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('problems', $data);
        $this->assertArrayHasKey('count', $data);
    }

    /**
     * @group functional
     */
    public function testGetProblemReturnsJsonWithCorrectKeys()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/problems/problem_check_long_ago');
        $response = $client->getResponse();
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('problem', $data);
        $this->assertArrayHasKey('id', $data['problem']);
        $this->assertSame('problem_check_long_ago', $data['problem']['id']);
    }

    /**
     * @group functional
     */
    public function testGetProblemRespondsWith404ForUnknownProblem()
    {
        $client = static::createClient(array(), array('HTTP_APIKEY' => 'swentz'));

        $client->request('GET', '/admin/rest/problems/invalid');
        $response = $client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
    }
}
