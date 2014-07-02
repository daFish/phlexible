<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Menu;

/**
 * Menu item
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MenuItem
{
    /**
     * @var string
     */
    private $xtype;

    /**
     * @var string
     */
    private $parent;

    /**
     * @var array
     */
    private $resources = array();

    /**
     * @var array
     */
    private $parameters = array();

    /**
     * @var MenuItemCollection
     */
    private $items = array();

    /**
     * @param string $xtype
     * @param null   $parent
     * @param array  $resources
     */
    public function __construct($xtype, $parent = null, array $resources = array())
    {
        $this->xtype = $xtype;
        $this->parent = $parent;
        $this->resources = $resources;
    }

    /**
     * @return string
     */
    public function getXtype()
    {
        return $this->xtype;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return array
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * @param MenuItemCollection $items
     *
     * @return $this
     */
    public function setItems(MenuItemCollection $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return MenuItemCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}