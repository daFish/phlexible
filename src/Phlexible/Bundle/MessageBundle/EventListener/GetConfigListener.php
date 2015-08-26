<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;
use Phlexible\Bundle\MessageBundle\Model\MessageManagerInterface;

/**
 * Get config listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigListener
{
    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(MessageManagerInterface $messageManager)
    {
        $this->messageManager = $messageManager;
    }

    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $event->getConfig()
            ->set('message.types', $this->messageManager->getTypeNames());
    }
}
