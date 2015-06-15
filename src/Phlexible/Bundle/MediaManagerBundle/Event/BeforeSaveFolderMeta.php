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

use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Before save folder meta event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeSaveFolderMetaEvent extends Event
{
    /**
     * @var ExtendedFolderInterface
     */
    private $folder;

    /**
     * @param ExtendedFolderInterface $folder
     */
    public function __construct(ExtendedFolderInterface $folder)
    {
        $this->folder = $folder;
    }

    /**
     * @return ExtendedFolderInterface
     */
    public function getFolder()
    {
        return $this->folder;
    }
}
