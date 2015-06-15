<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Abstract field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractField extends Field
{
    /**
     * {@inheritdoc}
     */
    public function isContainer()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isField()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getDataType()
    {
        return 'string';
    }

    /**
     * @param string $value
     *
     * @return mixed
     */
    public function fromRaw($value)
    {
        $type = $this->getDataType();

        if ($type === 'array') {
            $value = json_decode($value, true);
        } elseif ($type === 'boolean') {
            $value = (bool) $value;
        } elseif ($type === 'integer') {
            $value = (int) $value;
        } elseif ($type === 'float') {
            $value = (float) $value;
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function toRaw($value)
    {
        if ($this->getDataType() === 'array') {
            return json_encode($value);
        }

        return $value;
    }
}
