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
    private $names = array();

    /**
     * @var array
     */
    private $ids = array();

    /**
     * @var array
     */
    private $dsIds = array();

    /**
     * @param MainClassDefinition $class
     * @param string              $filename
     */
    public function addMainClass(MainClassDefinition $class, $filename)
    {
        $this->ids[$class->getElementtypeId()] = array(
            'classname' => $class->getNamespace() . '\\' . $class->getClassname(),
            'filename'  => $filename
        );
        $this->names[] = $class->getElementtypeName();
    }

    /**
     * @return array
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @param StructureClassDefinition $class
     * @param string                   $filename
     */
    public function addStructureClass(StructureClassDefinition $class, $filename)
    {
        $this->dsIds[$class->getDsId()] = array(
            'classname' => $class->getNamespace() . '\\' . $class->getClassname(),
            'filename'  => $filename
        );
    }

    /**
     * @return string
     */
    public function getDsIds()
    {
        return $this->dsIds;
    }
}
