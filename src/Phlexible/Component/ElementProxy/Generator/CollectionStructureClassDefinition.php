<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\ElementProxy\Generator;

/**
 * Collection structure class definition
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CollectionStructureClassDefinition extends StructureClassDefinition
{
    /**
     * @param string            $classname
     * @param string            $namespace
     * @param ValueDefinition[] $values
     * @param array             $children
     * @param string            $name
     * @param string            $dsId
     */
    public function __construct($classname, $namespace, array $values, array $children, $name, $dsId)
    {
        parent::__construct(
            $classname,
            $namespace,
            $values,
            $children['classes'],
            $children['collections'],
            $name,
            $dsId
        );
    }
}
