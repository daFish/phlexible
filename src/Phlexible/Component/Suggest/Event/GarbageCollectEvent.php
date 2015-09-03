<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Suggest\Event;

use Phlexible\Component\Suggest\Domain\DataSourceValueBag;
use Symfony\Component\EventDispatcher\Event;

/**
 * Garbage collect event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GarbageCollectEvent extends Event
{
    /**
     * @var DataSourceValueBag
     */
    private $values;

    /**
     * @var array
     */
    private $activeValues = array();

    /**
     * @var array
     */
    private $inactiveValues = array();

    /**
     * @param DataSourceValueBag $values
     */
    public function __construct(DataSourceValueBag $values)
    {
        $this->values = $values;
    }

    /**
     * @return DataSourceValueBag
     */
    public function getDataSourceValueBag()
    {
        return $this->values;
    }

    /**
     * @param string|array $values
     *
     * @return $this
     */
    public function markActive($values)
    {
        if (!is_array($values)) {
            $values = array($values);
        }

        foreach ($values as $value) {
            $this->activeValues[] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getActiveValues()
    {
        return $this->activeValues;
    }

    /**
     * @param string|array $values
     *
     * @return $this
     */
    public function markInactive($values)
    {
        if (!is_array($values)) {
            $values = array($values);
        }

        foreach ($values as $value) {
            $this->inactiveValues[] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getInactiveValues()
    {
        return $this->inactiveValues;
    }
}
