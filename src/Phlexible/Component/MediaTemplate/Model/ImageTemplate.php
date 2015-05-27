<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\Model;

/**
 * Image template
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImageTemplate extends AbstractTemplate
{
    const TYPE_IMAGE = 'image';

    /**
     * @var int
     */
    private $width = 0;

    /**
     * @var int
     */
    private $height = 0;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $scale;

    /**
     * @var bool
     */
    private $forWeb = false;

    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $colorspace;

    /**
     * @var string
     */
    private $tiffCompression;

    /**
     * @var string
     */
    private $depth;

    /**
     * @var string
     */
    private $quality;

    /**
     * @var string
     */
    private $backgroundcolor;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->setType(self::TYPE_IMAGE);
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     *
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     *
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @param string $scale
     *
     * @return $this
     */
    public function setScale($scale)
    {
        $this->scale = $scale;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isForWeb()
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
     * @return string
     */
    public function getColorspace()
    {
        return $this->colorspace;
    }

    /**
     * @param string $colorspace
     *
     * @return $this
     */
    public function setColorspace($colorspace)
    {
        $this->colorspace = $colorspace;

        return $this;
    }

    /**
     * @return string
     */
    public function getTiffCompression()
    {
        return $this->tiffCompression;
    }

    /**
     * @param string $tiffCompression
     *
     * @return $this
     */
    public function setTiffCompression($tiffCompression)
    {
        $this->tiffCompression = $tiffCompression;

        return $this;
    }

    /**
     * @return string
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param string $depth
     *
     * @return $this
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * @param string $quality
     *
     * @return $this
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * @return string
     */
    public function getBackgroundcolor()
    {
        return $this->backgroundcolor;
    }

    /**
     * @param string $backgroundcolor
     *
     * @return $this
     */
    public function setBackgroundcolor($backgroundcolor)
    {
        $this->backgroundcolor = $backgroundcolor;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array(
            'width'            => $this->width,
            'height'           => $this->height,
            'method'           => $this->method,
            'scale'            => $this->scale,
            'for_web'          => $this->forWeb,
            'format'           => $this->format,
            'colorspace'       => $this->colorspace,
            'tiff_compression' => $this->tiffCompression,
            'depth'            => $this->depth,
            'quality'          => $this->quality,
            'backgroundcolor'  => $this->backgroundcolor,
        );
    }
}
