<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy\Generator;

/**
 * Value definition
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ValueDefinition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $rawName;

    /**
     * @var string
     */
    private $dsId;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $dataType;

    /**
     * @param string $name
     * @param string $rawName
     * @param string $dsId
     * @param string $type
     * @param string $dataType
     */
    public function __construct($name, $rawName, $dsId, $type, $dataType)
    {
        $this->name = $name;
        $this->rawName = $rawName;
        $this->dsId = $dsId;
        $this->type = $type;
        $this->dataType = $dataType;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUpperName()
    {
        return ucfirst($this->name);
    }

    /**
     * @return string
     */
    public function getRawName()
    {
        return $this->rawName;
    }

    /**
     * @return string
     */
    public function getDsId()
    {
        return $this->dsId;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }
}
