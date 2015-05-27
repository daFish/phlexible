<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\Model;

/**
 * Audio template
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AudioTemplate extends AbstractTemplate
{
    const TYPE_AUDIO = 'audio';

    /**
     * @var string
     */
    private $audioFormat;

    /**
     * @var string
     */
    private $audioBitrate;

    /**
     * @var string
     */
    private $audioSamplerate;

    /**
     * @var string
     */
    private $audioSamplebits;

    /**
     * @var int
     */
    private $audioChannels;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->setType(self::TYPE_AUDIO);
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
    public function toArray()
    {
        return array(
            'audio_bitrate'    => $this->audioBitrate,
            'audio_channels'   => $this->audioChannels,
            'audio_samplebits' => $this->audioSamplebits,
            'audio_samplerate' => $this->audioSamplerate,
        );
    }
}
