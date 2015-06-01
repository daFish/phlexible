<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaBundle\MediaClassifier\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Temp\MediaClassifier\Loader\LoaderInterface;

/**
 * Media bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class KernelLoader implements LoaderInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var FileLocatorInterface
     */
    private $locator;

    /**
     * @param LoaderInterface      $loader
     * @param FileLocatorInterface $locator
     */
    public function __construct(LoaderInterface $loader, FileLocatorInterface $locator)
    {
        $this->loader = $loader;
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($file)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function load($filename)
    {
        return $this->loader->load($this->locator->locate($filename, null, true));
    }
}
