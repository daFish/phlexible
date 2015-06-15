<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Templating\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\Asset\PathPackage as BasePathPackage;

/**
 * Path package
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PathPackage extends BasePathPackage
{
    /**
     * Constructor.
     *
     * @param Request $request The current request
     * @param string  $path    The path
     * @param string  $version The version
     * @param string  $format  The version format
     */
    public function __construct(Request $request, $path, $version = null, $format = null)
    {
        parent::__construct($request->getBasePath() . $path, $version, $format);
    }
}
