<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Message\Tests\Handler;

use Phlexible\Component\Message\Domain\Message;
use Phlexible\Component\Message\Handler\LogHandler;
use Prophecy\Argument;

/**
 * Log handler test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LogHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleMessageWithTypeInfo()
    {
        $message = new Message('s', 'b', 0, 'c', 'r', 'u', new \DateTime());

        $logger = $this->prophesize('\Psr\Log\LoggerInterface');
        $logger->info(Argument::type('string'))->shouldBeCalled();

        $handler = new LogHandler($logger->reveal());

        $handler->handle($message);
    }

    public function testHandleMessageWithTypeError()
    {
        $message = new Message('s', 'b', 1, 'c', 'r', 'u', new \DateTime());

        $logger = $this->prophesize('\Psr\Log\LoggerInterface');
        $logger->error(Argument::type('string'))->shouldBeCalled();

        $handler = new LogHandler($logger->reveal());

        $handler->handle($message);
    }

    public function testCloseDelegatesToChildHandlerAndCallsClose()
    {
        $message = new Message('s', 'b', 1, 'c', 'r', 'u', new \DateTime());

        $logger = $this->prophesize('\Psr\Log\LoggerInterface');

        $handler = new LogHandler($logger->reveal());

        $handler->handle($message);
        $handler->close();
    }
}
