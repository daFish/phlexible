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

use Phlexible\Bundle\ProblemBundle\Controller\ProblemsController;
use Phlexible\Bundle\ProblemBundle\Entity\Problem;

/**
 * Problems controller test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ProblemsControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetProblemsReturnsJsonWithCorrectKeys()
    {
        $fetcher = $this->prophesize('Phlexible\Bundle\ProblemBundle\Problem\ProblemFetcherInterface');
        $fetcher->fetch()->willReturn(array());

        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->get('phlexible_problem.problem_fetcher')->willReturn($fetcher->reveal());

        $controller = new ProblemsController();
        $controller->setContainer($container->reveal());

        $data = $controller->getProblemsAction();

        $this->assertArrayHasKey('problems', $data);
        $this->assertArrayHasKey('count', $data);
    }

    public function testGetProblemReturnsJsonWithCorrectKeys()
    {
        $problem = new Problem('valid', Problem::SEVERITY_INFO, 'test');

        $fetcher = $this->prophesize('Phlexible\Bundle\ProblemBundle\Problem\ProblemFetcherInterface');
        $fetcher->fetch()->willReturn(array($problem));

        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->get('phlexible_problem.problem_fetcher')->willReturn($fetcher->reveal());

        $controller = new ProblemsController();
        $controller->setContainer($container->reveal());

        $data = $controller->getProblemAction('valid');

        $this->assertArrayHasKey('problem', $data);
        $this->assertSame('valid', $data['problem']->getId());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetProblemRespondsWith404ForUnknownProblem()
    {
        $fetcher = $this->prophesize('Phlexible\Bundle\ProblemBundle\Problem\ProblemFetcherInterface');
        $fetcher->fetch()->willReturn(array());

        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->get('phlexible_problem.problem_fetcher')->willReturn($fetcher->reveal());

        $controller = new ProblemsController();
        $controller->setContainer($container->reveal());

        $controller->getProblemAction('invalid');
    }
}
