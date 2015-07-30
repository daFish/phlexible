<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
     * @param \Phlexible\Component\MediaTemplate\Domain\AudioTemplate $template
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
     * @param \Phlexible\Component\MediaTemplate\Domain\AudioTemplate $template
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
