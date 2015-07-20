<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy;

/**
 * Structure interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface StructureInterface
{
    /**
     * @return array
     */
    public function __getValues();

    /**
     * @return array
     */
    public function __getValueDescriptors();

    /**
     * @param array $values
     */
    public function __setValues(array $values);

    /**
     * @return ChildStructureInterface[]
     */
    public function __getChildren();

    /**
     * @return array
     */
    public function __toArray();
}
