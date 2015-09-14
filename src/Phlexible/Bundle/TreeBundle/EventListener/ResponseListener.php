<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Routing\RouterInterface;

/**
 * Response listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ResponseListener
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->has('contentDocument')) {
            return;
        }

        $response = $event->getResponse();
        $content = $response->getContent();

        if (!preg_match_all('/\[(tid=\d+.*?)\]/', $content, $match)) {
            return;
        }

        foreach ($match[1] as $index => $link) {
            $nodeId = null;
            $parameters = array();

            $parts = explode(',', $link);
            foreach ($parts as $part) {
                list($key, $value) = explode('=', $part);
                if ($key === 'tid') {
                    $nodeId = (int) $value;
                } elseif ($key === 'language') {
                    $parameters['_locale'] = $value;
                } else {
                    $parameters[$key] = $value;
                }
            }

            if ($nodeId) {
                $url = $this->router->generate($nodeId, $parameters);
                $content = str_replace($match[0][$index], $url, $content);
            }
        }

        $response->setContent($content);
    }
}
