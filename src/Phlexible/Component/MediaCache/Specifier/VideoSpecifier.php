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

use Phlexible\Component\MediaTemplate\Domain\VideoTemplate;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Temp\MediaConverter\Format\Video;

/**
 * Video specifier.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VideoSpecifier implements SpecifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template)
    {
        return $template instanceof VideoTemplate;
    }

    /**
     * {@inheritdoc}
     *
     * @param VideoTemplate $template
     */
    public function getExtension(TemplateInterface $template)
    {
        $extension = $template->getVideoFormat();

        if (!$extension) {
            $extension = 'mp4';
        }

        return $extension;
    }

    /**
     * {@inheritdoc}
     *
     * @param VideoTemplate $template
     */
    public function specify(TemplateInterface $template)
    {
        $spec = new Video();

        if ($template->getVideoFormat()) {
            $spec->setVideoFormat($template->getVideoFormat());
        }

        if ($template->getResizeMethod()) {
            $spec->setResizeMode($template->getResizeMethod());
        }

        if ($template->getVideoWidth()) {
            $spec->setWidth($template->getVideoWidth());
        }

        if ($template->getVideoHeight()) {
            $spec->setHeight($template->getVideoHeight());
        }

        if ($template->getVideoBitrate()) {
            $spec->setVideoBitrate($template->getVideoBitrate());
        }

        if ($template->getVideoFramerate()) {
            $spec->setVideoFramerate($template->getVideoFramerate());
            $spec->setVideoGop(25);
        }

        if ($template->getAudioFormat()) {
            $spec->setAudioFormat($template->getAudioFormat());
        }

        if ($template->getAudioBitrate()) {
            $spec->setAudioBitrate($template->getAudioBitrate());
        }

        if ($template->getAudioChannels()) {
            $spec->setAudioChannels($template->getAudioChannels());
        }

        if ($template->getAudioSamplerate()) {
            $spec->setAudioSamplerate($template->getAudioSamplerate());
        }

        return $spec;
    }
}
