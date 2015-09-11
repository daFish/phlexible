<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaTypeBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\ViewEvent;
use Symfony\Component\Routing\RouterInterface;

/**
 * View frame listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ViewFrameListener
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
     * @param ViewEvent $event
     */
    public function onViewFrame(ViewEvent $event)
    {
        $view = $event->getView();

        $view
            ->addScript($this->router->generate('phlexible_mediatype_asset_scripts'))
            ->addLink($this->router->generate('phlexible_mediatype_asset_css'));
    }
}
