<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Menu;

use Phlexible\Bundle\GuiBundle\Event\GetMenuEvent;
use Phlexible\Bundle\GuiBundle\GuiEvents;
use Phlexible\Bundle\GuiBundle\Menu\Loader\DelegatingLoader;
use Phlexible\Bundle\GuiBundle\Menu\Loader\LoaderResolver;
use Phlexible\Bundle\GuiBundle\Menu\Loader\YamlFileLoader;
use Puli\Discovery\Api\ResourceDiscovery;
use Puli\Repository\Resource\FileResource;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Menu loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MenuLoader
{
    /**
     * @var ResourceDiscovery
     */
    private $puliDiscovery;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param ResourceDiscovery        $puliDiscovery
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(ResourceDiscovery $puliDiscovery, EventDispatcherInterface $dispatcher)
    {
        $this->puliDiscovery = $puliDiscovery;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return MenuItemCollection
     */
    public function load()
    {
        $loader = new DelegatingLoader(
            new LoaderResolver(
                [
                    new YamlFileLoader(),
                ]
            )
        );
        $items = new MenuItemCollection();
        foreach ($this->puliDiscovery->findByType('phlexible/menu') as $binding) {
            foreach ($binding->getResources() as $resource) {
                /* @var $resource FileResource */

                $loadedItems = $loader->load($resource->getFilesystemPath());
                $items->merge($loadedItems);
            }
        }

        $event = new GetMenuEvent($items);
        $this->dispatcher->dispatch(GuiEvents::GET_MENU, $event);

        $sorter = new HierarchicalSorter();
        $items = $sorter->sort($items);

        return $items;
    }
}
