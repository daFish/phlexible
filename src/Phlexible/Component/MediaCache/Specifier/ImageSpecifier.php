<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Specifier;

use Phlexible\Component\MediaTemplate\Domain\ImageTemplate;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Temp\MediaConverter\Format\Image;

/**
 * Image specifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImageSpecifier implements SpecifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template)
    {
        return $template instanceof ImageTemplate;
    }

    /**
     * {@inheritdoc}
     * @param ImageTemplate $template
     */
    public function getExtension(TemplateInterface $template)
    {
        $extension = $template->getFormat();

        if (!$extension) {
            $extension = 'jpg';
        }

        return $extension;
    }

    /**
     * {@inheritdoc}
     * @param ImageTemplate $template
     */
    public function specify(TemplateInterface $template)
    {
        $spec = new Image();

        if ($template->getMethod()) {
            $spec->setResizeMode($template->getMethod());
        }

        if ($template->getQuality()) {
            $spec->setQuality($template->getQuality());
        }

        if ($template->getColorspace()) {
            $spec->setColorspace($template->getColorspace());
        }

        if ($template->getFormat()) {
            $spec->setFormat($template->getFormat());
        }

        if ($template->getWidth()) {
            $spec->setWidth($template->getWidth());
        }

        if ($template->getHeight()) {
            $spec->setHeight($template->getHeight());
        }

        if ($template->getBackgroundcolor()) {
            $spec->setBackgroundColor($template->getBackgroundcolor());
        }

        return $spec;
    }
}
