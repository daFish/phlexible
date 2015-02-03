<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\EventListener;

use Phlexible\Bundle\ElementRendererBundle\Configurator\ConfiguratorInterface;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Exception listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExceptionListener
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var ConfiguratorInterface
     */
    private $configurator;

    /**
     * @var ContentTreeManagerInterface
     */
    private $treeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param \Twig_Environment           $twig
     * @param ConfiguratorInterface       $configurator
     * @param ContentTreeManagerInterface $treeManager
     * @param LoggerInterface             $logger
     * @param boolean                     $debug
     */
    public function __construct(
        \Twig_Environment $twig,
        ConfiguratorInterface $configurator,
        ContentTreeManagerInterface $treeManager,
        LoggerInterface $logger = null,
        $debug = false)
    {
        $this->twig = $twig;
        $this->configurator = $configurator;
        $this->treeManager = $treeManager;
        $this->logger = $logger;
        $this->debug = $debug;
    }

    /**
     * Handles security related exceptions.
     *
     * @param GetResponseForExceptionEvent $event An GetResponseForExceptionEvent instance
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        static $handling;

        if (true === $handling) {
            return;
        }

        $handling = true;

        $request = $event->getRequest();

        // Only for debug
        if ($this->debug) {
            return;
        }

        // Not for xml http requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        // Only for phlexible tree nodes
        if (!$request->attributes->has('siterootUrl')) {
            return;
        }

        $code = $event->getException()->getCode();
        if (!in_array($code, array(403, 404, 500))) {
            $code = 500;
        }

        $siteroot = $request->attributes->get('siterootUrl')->getSiteroot();
        $tid = $siteroot->getSpecialTid($request->getLocale(), "error_$code");
        if (!$tid) {
            return;
        }

        $treeNode = $this->treeManager->findByTreeId($tid)->get($tid);

        $request->attributes->set('tid', $tid);
        $request->attributes->set('routeDocument', $treeNode);
        $request->attributes->set('contentDocument', $treeNode);

        $configuration = $this->configurator->configure($request);
        if ($configuration->hasResponse()) {
            $event->setResponse($configuration->getResponse());

            return;
        }

        $data = $configuration->getVariables();

        $content = $this->twig->render("::error/$code.html.twig", $data);
        $response = new Response($content, 500);

        $event->setResponse($response);
    }
}