<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\DashboardBundle\Infobar;

/**
 * Infobar
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
class Infobar
{
    const REGION_HEADER = 'header';
    const REGION_FOOTER = 'footer';

    const TYPE_DEFAULT = '';
    const TYPE_NOTICE  = 'notice';
    const TYPE_URGENT  = 'urgent';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $xtype;

    /**
     * @var string
     */
    protected $region;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $data;

    /**
     * Return identifier
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return xtype
     *
     * @return string
     */
    public function getXtype()
    {
        return $this->xtype;
    }

    /**
     * Return region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Return type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return array representation of this infobar
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'     => $this->getID(),
            'xtype'  => $this->getXtype(),
            'region' => $this->getRegion(),
            'type'   => $this->getType(),
            'data'   => $this->getData(),
        );
    }
}
