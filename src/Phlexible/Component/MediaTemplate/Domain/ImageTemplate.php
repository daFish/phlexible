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
 * Image template.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Serializer\XmlRoot(name="mediaTemplate")
 * @Serializer\ExclusionPolicy("all")
 */
class ImageTemplate extends AbstractTemplate
{
    /**
     * @var int
     * @Serializer\Expose()
     * @Serializer\Type("integer")
     */
    private $width;

    /**
     * @var int
     * @Serializer\Expose()
     * @Serializer\Type("integer")
     */
    private $height;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $method;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $scale;

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
    private $format;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $colorspace;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $tiffCompression;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $depth;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $quality;

    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    private $backgroundcolor;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'image';
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
     * @return bool
     */
    public function isForWeb()
    {
        return $this->forWeb;
    }

    /**
     * @param bool $forWeb
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
    public function getParameters()
    {
        return array(
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
            'method' => $this->getMethod(),
            'scale' => $this->getScale(),
            'for_web' => $this->isForWeb(),
            'format' => $this->getFormat(),
            'colorspace' => $this->getColorspace(),
            'tiff_compression' => $this->getTiffCompression(),
            'depth' => $this->getDepth(),
            'quality' => $this->getQuality(),
            'backgroundcolor' => $this->getBackgroundcolor(),
        );
    }
}
