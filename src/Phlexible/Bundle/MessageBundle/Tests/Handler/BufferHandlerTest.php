<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Tests\Handler;

use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Handler\BufferHandler;

/**
 * Buffer handler test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BufferHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleStoresMessages()
    {
        $message = new Message('s', 'b', 1, 'c', 'r', 'u', new \DateTime());

        $childHandler = $this->prophesize('\Phlexible\Bundle\MessageBundle\Handler\HandlerInterface');
        $childHandler->handle($message)->shouldNotBeCalled();

        $handler = new BufferHandler($childHandler->reveal());

        $handler->handle($message);

        $this->assertAttributeContains($message, 'messages', $handler);
    }

    public function testCloseDelegatesToChildHandlerAndCallsClose()
    {
        $message = new Message('s', 'b', 1, 'c', 'r', 'u', new \DateTime());

        $childHandler = $this->prophesize('\Phlexible\Bundle\MessageBundle\Handler\HandlerInterface');
        $childHandler->handle($message)->shouldBeCalled();
        $childHandler->close()->shouldBeCalled();

        $handler = new BufferHandler($childHandler->reveal());

        $handler->handle($message);
        $handler->close();
    }
}