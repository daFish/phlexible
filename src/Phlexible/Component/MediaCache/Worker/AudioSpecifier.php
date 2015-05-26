<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\Worker;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Audio specifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AudioSpecifier implements SpecifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function specify(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        $spec = new Audio();

        return $spec;
    }
}
