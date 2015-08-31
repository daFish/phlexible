<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\Domain;

use JMS\Serializer\Annotation as Serializer;

/**
 * Video template
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Serializer\XmlRoot(name="mediaTemplate")
 * @Serializer\ExclusionPolicy("all")
 */
class VideoTemplate extends AbstractTemplate
{
    /**
     * @var bool
     * @Serializer\Expose()
     * @Serializer\Type("boolean")
     */
    private $matchFormat = false;

    /**
     * @var bool
     * @Serializer\Expose()
     * @Serializer\Type("boolean")
     */
    private $forWeb = false;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $format = 'flv';

    /**
     * @var bool
     * @Serializer\Expose()
     * @Serializer\Type("boolean")
     */
    private $deinterlace = false;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $resizeMethod;

    /**
     * @var int
     * @Serializer\Expose()
     * @Serializer\Type("integer")
     */
    private $videoWidth;

    /**
     * @var int
     * @Serializer\Expose()
     * @Serializer\Type("integer")
     */
    private $videoHeight;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $videoFormat = 'mp4';

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $videoBitrate;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $videoFramerate;

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
        return 'video';
    }

    /**
     * @return boolean
     */
    public function getMatchFormat()
    {
        return $this->matchFormat;
    }

    /**
     * @param boolean $matchFormat
     *
     * @return $this
     */
    public function setMatchFormat($matchFormat)
    {
        $this->matchFormat = $matchFormat;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getForWeb()
    {
        return $this->forWeb;
    }

    /**
     * @param boolean $forWeb
     *
     * @return $this
     */
    public function setForWeb($forWeb)
    {
        $this->forWeb = $forWeb;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDeinterlace()
    {
        return $this->deinterlace;
    }

    /**
     * @param boolean $deinterlace
     *
     * @return $this
     */
    public function setDeinterlace($deinterlace)
    {
        $this->deinterlace = $deinterlace;

        return $this;
    }

    /**
     * @return string
     */
    public function getResizeMethod()
    {
        return $this->resizeMethod;
    }

    /**
     * @param string $resizeMethod
     *
     * @return $this
     */
    public function setResizeMethod($resizeMethod)
    {
        $this->resizeMethod = $resizeMethod;

        return $this;
    }

    /**
     * @return int
     */
    public function getVideoWidth()
    {
        return $this->videoWidth;
    }

    /**
     * @param int $videoWidth
     *
     * @return $this
     */
    public function setVideoWidth($videoWidth)
    {
        $this->videoWidth = $videoWidth;

        return $this;
    }

    /**
     * @return int
     */
    public function getVideoHeight()
    {
        return $this->videoHeight;
    }

    /**
     * @param int $videoHeight
     *
     * @return $this
     */
    public function setVideoHeight($videoHeight)
    {
        $this->videoHeight = $videoHeight;

        return $this;
    }

    /**
     * @return string
     */
    public function getVideoFormat()
    {
        return $this->videoFormat;
    }

    /**
     * @param string $videoFormat
     *
     * @return $this
     */
    public function setVideoFormat($videoFormat)
    {
        $this->videoFormat = $videoFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getVideoBitrate()
    {
        return $this->videoBitrate;
    }

    /**
     * @param string $videoBitrate
     *
     * @return $this
     */
    public function setVideoBitrate($videoBitrate)
    {
        $this->videoBitrate = $videoBitrate;

        return $this;
    }

    /**
     * @return string
     */
    public function getVideoFramerate()
    {
        return $this->videoFramerate;
    }

    /**
     * @param string $videoFramerate
     *
     * @return $this
     */
    public function setVideoFramerate($videoFramerate)
    {
        $this->videoFramerate = $videoFramerate;

        return $this;
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
            'match_format'    => $this->getMatchFormat(),
            'for_web'         => $this->getForWeb(),
            'format'          => $this->getFormat(),
            'deinterlace'     => $this->getDeinterlace(),
            'video_width'     => $this->getVideoWidth(),
            'video_height'    => $this->getVideoHeight(),
            'video_format'    => $this->getVideoFormat(),
            'video_bitrate'   => $this->getVideoBitrate(),
            'video_framerate' => $this->getVideoFramerate(),
            'audio_bitrate'    => $this->getAudioBitrate(),
            'audio_channels'   => $this->getAudioChannels(),
            'audio_samplebits' => $this->getAudioSamplebits(),
            'audio_samplerate' => $this->getAudioSamplerate(),
        );
    }
}
