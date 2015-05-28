<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\Model;

/**
 * Video template
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VideoTemplate extends AbstractTemplate
{
    const TYPE_VIDEO = 'video';

    /**
     * @var bool
     */
    private $matchFormat = false;

    /**
     * @var bool
     */
    private $forWeb = false;

    /**
     * @var string
     */
    private $format = 'flv';

    /**
     * @var bool
     */
    private $deinterlace = false;

    /**
     * @var string
     */
    private $resizeMethod;

    /**
     * @var int
     */
    private $videoWidth;

    /**
     * @var int
     */
    private $videoHeight;

    /**
     * @var string
     */
    private $videoFormat = 'mp4';

    /**
     * @var string
     */
    private $videoBitrate;

    /**
     * @var string
     */
    private $videoFramerate;

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
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE_VIDEO;
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
    public function toArray()
    {
        return array(
            'match_format'    => $this->matchFormat,
            'for_web'         => $this->forWeb,
            'format'          => $this->format,
            'deinterlace'     => $this->deinterlace,
            'video_width'     => $this->videoWidth,
            'video_height'    => $this->videoHeight,
            'video_format'    => $this->videoFormat,
            'video_bitrate'   => $this->videoBitrate,
            'video_framerate' => $this->videoFramerate,
            'audio_bitrate'    => $this->audioBitrate,
            'audio_channels'   => $this->audioChannels,
            'audio_samplebits' => $this->audioSamplebits,
            'audio_samplerate' => $this->audioSamplerate,
        );
    }
}
