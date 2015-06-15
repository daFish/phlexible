<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaType\Model;

use Brainbits\Mime\InternetMediaType;

/**
 * Media type
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaType
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $svg;

    /**
     * @var array
     */
    private $titles = [];

    /**
     * @var InternetMediaType[]
     */
    private $mimetypes = [];

    /**
     * @var array
     */
    private $icons = [];

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     *
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getSvg()
    {
        return $this->svg;
    }

    /**
     * @param string $svg
     *
     * @return $this
     */
    public function setSvg($svg)
    {
        $this->svg = $svg;

        return $this;
    }

    /**
     * @return array
     */
    public function getTitles()
    {
        return $this->titles;
    }

    /**
     * @param array $titles
     *
     * @return $this
     */
    public function setTitles(array $titles)
    {
        $this->titles = $titles;

        return $this;
    }

    /**
     * Return localized title
     *
     * @param string $code
     *
     * @return string
     */
    public function getTitle($code)
    {
        if (!isset($this->titles[$code])) {
            $code = key($this->getTitles());
        }

        return $this->titles[$code];
    }

    /**
     * Set title
     *
     * @param string $code
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($code, $title)
    {
        $this->titles[$code] = $title;

        return $this;
    }

    /**
     * @return InternetMediaType[]
     */
    public function getMimetypes()
    {
        return $this->mimetypes;
    }

    /**
     * @param InternetMediaType[] $mimetypes
     *
     * @return $this
     */
    public function setMimetypes(array $mimetypes)
    {
        $this->mimetypes = $mimetypes;

        return $this;
    }

    /**
     * @param InternetMediaType $mimetype
     */
    public function addMimetype(InternetMediaType $mimetype)
    {
        $this->mimetypes[] = $mimetype;
    }

    /**
     * @return string
     */
    public function getMimetype()
    {
        if (count($this->mimetypes)) {
            return reset($this->mimetypes);
        }

        return InternetMediaType::fromString('application/octet-stream');
    }

    /**
     * @return array
     */
    public function getIcons()
    {
        return $this->icons;
    }

    /**
     * @param array $icons
     *
     * @return $this
     */
    public function setIcons(array $icons)
    {
        $this->icons = $icons;

        return $this;
    }

    /**
     * @param int    $size
     * @param string $icon
     */
    public function setIcon($size, $icon)
    {
        $this->icons[$size] = $icon;
    }
}
