<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
            array('/BASE_PATH/', '/BASE_URL/', '/BUNDLES_PATH/'),
            array($this->basePath, $this->baseUrl, $this->basePath . 'bundles/'),
            $content
        );
    }
}
