<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Event;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Before save meta event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeSaveMetaEvent extends Event
{
    //private $eventName = Events::BEFORE_SAVE_META;

    /**
     * @var ExtendedFileInterface
     */
    private $file;

    /**
     * @param ExtendedFileInterface $file
     */
    public function __construct(ExtendedFileInterface $file)
    {
        $this->file = $file;
    }

    /**
     * @return ExtendedFileInterface
     */
    public function getFile()
    {
        return $this->file;
    }
}
