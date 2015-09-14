<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
