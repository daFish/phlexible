<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy;

/**
 * Structure collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class StructureCollection
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $items;

    /**
     * @param string $id
     * @param string $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @param ChildStructureInterface $item
     */
    abstract protected function validateType(ChildStructureInterface $item);

    /**
     * @param ChildStructureInterface $item
     */
    public function add(ChildStructureInterface $item)
    {
        $this->validateType($item);

        $this->items[] = $item;
    }
    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
