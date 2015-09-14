<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
