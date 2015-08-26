<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy\Generator;

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
