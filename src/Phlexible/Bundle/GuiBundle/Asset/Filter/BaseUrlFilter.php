<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

/**
 * Filter phlexible baseurl and basepath
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BaseUrlFilter implements FilterInterface
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
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        $asset->setContent(
            str_replace(
                array('/makeweb/', '/BASEPATH/', '/BASEURL/', '/COMPONENTSPATH/'),
                array($this->basePath, $this->basePath, $this->baseUrl, $this->basePath . 'bundles/'),
                $asset->getContent()
            )
        );
    }
}