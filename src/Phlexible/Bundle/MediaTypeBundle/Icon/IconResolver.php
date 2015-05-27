<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTypeBundle\Icon;

use Symfony\Component\Config\FileLocatorInterface;
use Temp\MediaClassifier\Model\MediaType;

/**
 * Media type icon resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class IconResolver
{
    /**
     * @var FileLocatorInterface
     */
    private $locator;

    /**
     * @param FileLocatorInterface $locator
     */
    public function __construct(FileLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * Resolve icon
     *
     * @param MediaType $mediaType
     * @param int       $requestedSize
     *
     * @return string
     */
    public function resolve(MediaType $mediaType, $requestedSize = null)
    {
        $name = $mediaType->getName();

        $sizes = array(16, 32, 48, 256);
        $size = $requestedSize;
        $icon = $this->locator->locate("@PhlexibleMediaTypeBundle/Resources/public/mimetypes$size/$name.gif", null, true);

        if ($icon) {
            return $icon;
        }

        $icons = null;
        foreach ($sizes as $size) {
            if ($size > $requestedSize) {
                $icon = $this->locator->locate("@PhlexibleMediaTypeBundle/Resources/public/mimetypes$size/$name.gif", null, true);
                break;
            }
        }

        return $icon;
    }
}
