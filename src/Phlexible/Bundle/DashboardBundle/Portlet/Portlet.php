<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\DashboardBundle\Portlet;

/**
 * Portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Portlet
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $xtype;

    /**
     * @var string
     */
    private $iconClass;

    /**
     * @var string
     */
    private $role;

    /**
     * @var array
     */
    private $data = array();

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getXtype()
    {
        return $this->xtype;
    }

    /**
     * @param string $xtype
     *
     * @return $this
     */
    public function setXtype($xtype)
    {
        $this->xtype = $xtype;

        return $this;
    }

    /**
     * @return string
     */
    public function getIconClass()
    {
        return $this->iconClass;
    }

    /**
     * @param string $iconClass
     *
     * @return $this
     */
    public function setIconClass($iconClass)
    {
        $this->iconClass = $iconClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasRole()
    {
        return $this->role !== null;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return array();
    }

    /**
     * Return array representation of this portlet
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'       => $this->getId(),
            'xtype'    => $this->getXtype(),
            'iconCls'  => $this->getIconClass(),
            'data'     => $this->getData(),
            'settings' => $this->getSettings(),
        );
    }
}
