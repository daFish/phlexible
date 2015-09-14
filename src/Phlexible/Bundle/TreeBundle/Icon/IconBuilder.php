<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Icon;

use Symfony\Component\Config\FileLocatorInterface;

/**
 * Icon builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class IconBuilder
{
    /**
     * @var FileLocatorInterface
     */
    private $locator;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param FileLocatorInterface $locator
     * @param string               $cacheDir
     */
    public function __construct(FileLocatorInterface $locator, $cacheDir)
    {
        $this->locator = $locator;
        $this->cacheDir = $cacheDir;
    }

    /**
     * Create parameter icon
     *
     * @param string $filename
     * @param array  $params
     *
     * @return string
     */
    public function createParameterIcon($filename, array $params = array())
    {
        $overlays = array();

        if (!empty($params['status']) && in_array($params['status'], array('async', 'online'))) {
            $overlays['status'] = $params['status'];
        }

        if (!empty($params['lock'])) {
            $overlays['lock'] = 1;
        }

        if (!empty($params['instance'])) {
            $overlays['instance'] = 1;
        }

        $fallback = $this->locator->locate('@PhlexibleTreeBundle/Resources/public/node-icons/_fallback.gif');

        if (!$filename || !file_exists($filename)) {
            $filename = $fallback;
        }

        $cacheFilename = $this->cacheDir . '/' . md5(basename($filename) . '__' . json_encode($overlays)) . '.png';

        if (1 || !file_exists($cacheFilename) || (time() - filemtime($cacheFilename)) > 60 * 60 * 24 * 30) {
            $target = imagecreate(18, 18);
            $black = imagecolorallocate($target, 0, 0, 0);
            imagecolortransparent($target, $black);

            $iconSource = imagecreatefromgif($filename);
            imagecopy($target, $iconSource, 0, 0, 0, 0, 18, 18);
            imagedestroy($iconSource);

            $overlayDir = '@PhlexibleTreeBundle/Resources/public/overlays/';

            if (!empty($overlays['status'])) {
                // apply status overlay
                $overlayIcon = imagecreatefromgif(
                    $this->locator->locate($overlayDir . 'status_' . $overlays['status'] . '.gif')
                );
                imagecopy($target, $overlayIcon, 10, 10, 0, 0, 8, 8);
                imagedestroy($overlayIcon);
            }

            if (!empty($overlays['instance'])) {
                // apply alias overlay
                $overlayIcon = imagecreatefromgif(
                    $this->locator->locate($overlayDir . 'instance.gif')
                );
                imagecopy($target, $overlayIcon, 0, 10, 0, 0, 8, 8);
                imagedestroy($overlayIcon);
            }

            if (!empty($overlays['lock'])) {
                // apply lock overlay
                $overlayIcon = imagecreatefromgif(
                    $this->locator->locate($overlayDir . 'lock.gif')
                );
                imagecopy($target, $overlayIcon, 0, 0, 0, 0, 8, 8);
                imagedestroy($overlayIcon);
            }

            if (!file_exists($this->cacheDir)) {
                mkdir($this->cacheDir, 0777, true);
            }

            imagepng($target, $cacheFilename);
        }

        return $cacheFilename;
    }
}
