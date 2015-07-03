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
use Phlexible\Bundle\MessageBundle\Handler\ChainHandler;

/**
 * Chain handler test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ChainHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleDelegatesToAllChainedHandlers()
    {
        $message = new Message('s', 'b', 1, 'c', 'r', 'u', new \DateTime());

        $handler1 = $this->prophesize('\Phlexible\Bundle\MessageBundle\Handler\HandlerInterface');
        $handler1->handle($message)->shouldBeCalled();
        $handler2 = $this->prophesize('\Phlexible\Bundle\MessageBundle\Handler\HandlerInterface');
        $handler1->handle($message)->shouldBeCalled();

        $handler = new ChainHandler(array($handler1->reveal(), $handler2->reveal()));

        $handler->handle($message);
    }

    public function testCloseDelegatesToAllChainedHandlers()
    {
        $handler1 = $this->prophesize('\Phlexible\Bundle\MessageBundle\Handler\HandlerInterface');
        $handler1->close()->shouldBeCalled();
        $handler2 = $this->prophesize('\Phlexible\Bundle\MessageBundle\Handler\HandlerInterface');
        $handler1->close()->shouldBeCalled();

        $handler = new ChainHandler(array($handler1->reveal(), $handler2->reveal()));

        $handler->close();
    }
}
