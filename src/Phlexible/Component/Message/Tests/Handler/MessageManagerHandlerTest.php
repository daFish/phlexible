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
use Phlexible\Component\Message\Handler\MessageManagerHandler;

/**
 * Message manager handler test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageManagerHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleMessageWithTypeInfo()
    {
        $message = new Message('s', 'b', 0, 'c', 'r', 'u', new \DateTime());

        $messageManager = $this->prophesize('\Phlexible\Component\Message\Model\MessageManagerInterface');
        $messageManager->updateMessage($message)->shouldBeCalled();

        $handler = new MessageManagerHandler($messageManager->reveal());

        $handler->handle($message);
    }

    public function testCloseDelegatesToChildHandlerAndCallsClose()
    {
        $message = new Message('s', 'b', 1, 'c', 'r', 'u', new \DateTime());

        $messageManager = $this->prophesize('\Phlexible\Component\Message\Model\MessageManagerInterface');

        $handler = new MessageManagerHandler($messageManager->reveal());

        $handler->handle($message);
        $handler->close();
    }
}
