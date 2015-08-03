<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy\Generator;

/**
 * Manager definition
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ManagerDefinition
{
    /**
     * @var array
     */
    private $elementtypeIds = array();

    /**
     * @var array
     */
    private $dsIds = array();

    /**
     * @param string $elementtypeId
     * @param string $classname
     * @param string $filename
     */
    public function addElementtypeId($elementtypeId, $classname, $filename)
    {
        $this->elementtypeIds[$elementtypeId] = array('classname' => $classname, 'filename' => $filename);
    }

    /**
     * @return array
     */
    public function getElementtypeIds()
    {
        return $this->elementtypeIds;
    }

    /**
     * @param string $dsId
     * @param string $classname
     * @param string $filename
     */
    public function addDsId($dsId, $classname, $filename)
    {
        $this->dsIds[$dsId] = array('classname' => $classname, 'filename' => $filename);
    }

    /**
     * @return string
     */
    public function getDsIds()
    {
        return $this->dsIds;
    }
}
