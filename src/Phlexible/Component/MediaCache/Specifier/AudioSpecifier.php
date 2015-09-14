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

use Phlexible\Component\MediaTemplate\Domain\AudioTemplate;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Temp\MediaConverter\Format\Audio;

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
    public function accept(TemplateInterface $template)
    {
        return $template instanceof AudioTemplate;
    }

    /**
     * {@inheritdoc}
     * @param AudioTemplate $template
     */
    public function getExtension(TemplateInterface $template)
    {
        $extension = $template->getAudioFormat();

        if (!$extension) {
            $extension = 'mp3';
        }

        return $extension;
    }

    /**
     * {@inheritdoc}
     * @param AudioTemplate $template
     */
    public function specify(TemplateInterface $template)
    {
        $spec = new Audio();

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
