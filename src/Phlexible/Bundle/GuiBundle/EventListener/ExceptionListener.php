<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\GuiBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Response\ExceptionResponse;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Exception listener
 *
 * @author  Stephan Wentz <sw@brainbits.net>
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
