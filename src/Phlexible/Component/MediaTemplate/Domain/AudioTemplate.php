<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\Domain;

use JMS\Serializer\Annotation as Serializer;

/**
 * Audio template
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Serializer\XmlRoot(name="mediaTemplate")
 * @Serializer\ExclusionPolicy("all")
 */
class AudioTemplate extends AbstractTemplate
{
    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $audioFormat;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $audioBitrate;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $audioSamplerate;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $audioSamplebits;

    /**
     * @var int
     * @Serializer\Expose()
     * @Serializer\Type("integer")
     */
    private $audioChannels;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'audio';
    }

    /**
     * @return string
     */
    public function getAudioFormat()
    {
        return $this->audioFormat;
    }

    /**
     * @param string $audioFormat
     *
     * @return $this
     */
    public function setAudioFormat($audioFormat)
    {
        $this->audioFormat = $audioFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getAudioBitrate()
    {
        return $this->audioBitrate;
    }

    /**
     * @param string $audioBitrate
     *
     * @return $this
     */
    public function setAudioBitrate($audioBitrate)
    {
        $this->audioBitrate = $audioBitrate;

        return $this;
    }

    /**
     * @return string
     */
    public function getAudioSamplerate()
    {
        return $this->audioSamplerate;
    }

    /**
     * @param string $audioSamplerate
     *
     * @return $this
     */
    public function setAudioSamplerate($audioSamplerate)
    {
        $this->audioSamplerate = $audioSamplerate;

        return $this;
    }

    /**
     * @return string
     */
    public function getAudioSamplebits()
    {
        return $this->audioSamplebits;
    }

    /**
     * @param string $audioSamplebits
     *
     * @return $this
     */
    public function setAudioSamplebits($audioSamplebits)
    {
        $this->audioSamplebits = $audioSamplebits;

        return $this;
    }

    /**
     * @return int
     */
    public function getAudioChannels()
    {
        return $this->audioChannels;
    }

    /**
     * @param int $audioChannels
     *
     * @return $this
     */
    public function setAudioChannels($audioChannels)
    {
        $this->audioChannels = $audioChannels;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return array(
            'audio_bitrate'    => $this->getAudioBitrate(),
            'audio_channels'   => $this->getAudioChannels(),
            'audio_samplebits' => $this->getAudioSamplebits(),
            'audio_samplerate' => $this->getAudioSamplerate(),
        );
    }
}
