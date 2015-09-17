<?php


/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\Ffmpeg\Format\Video;

use FFMpeg\Format\Video\DefaultVideo;

/**
 * The Flv video format.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Flv extends DefaultVideo
{
    /**
     * @param string $audioCodec
     * @param string $videoCodec
     */
    public function __construct($audioCodec = 'libmp3lame', $videoCodec = 'flv')
    {
        $this
            ->setAudioCodec($audioCodec)
            ->setVideoCodec($videoCodec);
    }

    /**
     * {@inheritdoc}
     */
    public function supportBFrames()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableAudioCodecs()
    {
        return array('libmp3lame');
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableVideoCodecs()
    {
        return array('flv');
    }
}
