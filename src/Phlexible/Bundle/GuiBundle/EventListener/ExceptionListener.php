<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Response\ExceptionResponse;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Exception listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExceptionListener extends ContainerAware
{
    /**
     * on Kernel Exception
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        $response = new ExceptionResponse($event->getException());

        $event->setResponse($response);
    }
}
