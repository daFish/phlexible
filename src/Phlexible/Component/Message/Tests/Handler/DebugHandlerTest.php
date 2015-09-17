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
use Phlexible\Component\Message\Handler\DebugHandler;

/**
 * Debug handler test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DebugHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleStoresMessages()
    {
        $message1 = new Message('subject1', 'body1', 0, 'channel1', 'role1', 'user1', new \DateTime());
        $message2 = new Message('subject2', 'body2', 1, 'channel2', 'role2', 'user2', new \DateTime());

        $handler = new DebugHandler();

        $handler->handle($message1);
        $handler->handle($message2);

        $messages = $handler->getMessages();
        $this->assertSame(
            $messages,
            array(
                array(
                    'subject' => $message1->getSubject(),
                    'body' => $message1->getBody(),
                    'type' => $message1->getType(),
                    'typeName' => 'info',
                    'channel' => $message1->getChannel(),
                    'role' => $message1->getRole(),
                    'user' => $message1->getUser(),
                    'createdAt' => $message1->getCreatedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'subject' => $message2->getSubject(),
                    'body' => $message2->getBody(),
                    'type' => $message2->getType(),
                    'typeName' => 'error',
                    'channel' => $message2->getChannel(),
                    'role' => $message2->getRole(),
                    'user' => $message2->getUser(),
                    'createdAt' => $message2->getCreatedAt()->format('Y-m-d H:i:s'),
                ),
            )
        );
    }

    public function testCloseDelegatesToChildHandlerAndCallsClose()
    {
        $message = new Message('s', 'b', 1, 'c', 'r', 'u', new \DateTime());

        $handler = new DebugHandler();

        $handler->handle($message);
        $handler->close();
    }
}
