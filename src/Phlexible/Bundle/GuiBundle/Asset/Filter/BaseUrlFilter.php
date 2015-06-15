<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Filter;

/**
 * Filter phlexible baseurl and basepath
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BaseUrlFilter
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @param string $baseUrl
     * @param string $basePath
     */
    public function __construct($baseUrl, $basePath)
    {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
        $this->basePath = rtrim($basePath, '/') . '/';
    }

    /**
     * {@inheritdoc}
     */
    public function filter($content)
    {
        return str_replace(
            ['/BASE_PATH/', '/BASE_URL/', '/BUNDLES_PATH/'],
            [$this->basePath, $this->baseUrl, $this->basePath . 'bundles/'],
            $content
        );
    }
}
