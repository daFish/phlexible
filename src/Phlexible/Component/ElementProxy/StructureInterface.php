<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\ElementProxy;

/**
 * Structure interface.
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
