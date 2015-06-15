<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Worker;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Cache worker interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface WorkerInterface
{
    /**
     * Are the given template and asset supported?
     *
     * @param TemplateInterface     $template
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     *
     * @return bool
     */
    public function accept(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType);

    /**
     * Process template and file
     *
     * @param TemplateInterface     $template
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     *
     * @return CacheItem
     */
    public function process(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType);
}
