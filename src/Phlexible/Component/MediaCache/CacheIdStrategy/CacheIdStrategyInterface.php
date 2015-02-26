<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\CacheIdStrategy;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Cache id strategy interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface CacheIdStrategyInterface
{
    /**
     * @param TemplateInterface     $template
     * @param ExtendedFileInterface $file
     *
     * @return string
     */
    public function createCacheId(TemplateInterface $template, ExtendedFileInterface $file);
}
